<?php

namespace Yaro\EcommerceProject\GraphQL;

use Psr\Log\LoggerInterface;
use GraphQL\GraphQL as GraphQLBase;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use GraphQL\Type\Definition\InputObjectType;
use GraphQL\Type\Schema;
use GraphQL\Type\SchemaConfig;
use Yaro\EcommerceProject\GraphQL\Resolvers\ProductResolver;
use Yaro\EcommerceProject\GraphQL\Resolvers\CategoryResolver;
use Yaro\EcommerceProject\GraphQL\Resolvers\AttributeResolver;
use Yaro\EcommerceProject\GraphQL\Resolvers\PriceResolver;
use Yaro\EcommerceProject\GraphQL\Resolvers\OrderResolver;
use Throwable;

class GraphQL
{
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public static function handle($vars)
    {
        $logger = $GLOBALS['logger'] ?? null;
        if ($logger) {
            $graphqlInstance = new self($logger);
            return $graphqlInstance->execute();
        } else {
            throw new \RuntimeException("Logger not initialized.");
        }
    }

    private function execute()
    {
        header("Access-Control-Allow-Origin: http://localhost:5173");
        header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
        header("Access-Control-Allow-Headers: Content-Type, Authorization");
        header("Access-Control-Max-Age: 86400");

        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            http_response_code(204);
            exit;
        }

        try {
            $this->logger->info("Request received: " . date('Y-m-d H:i:s'));

            $categoryResolver = new CategoryResolver($this->logger);
            $attributeResolver = new AttributeResolver($this->logger);
            $priceResolver = new PriceResolver();
            $orderResolver = new OrderResolver($this->logger);
            $productResolver = new ProductResolver(
                $categoryResolver,
                $attributeResolver,
                $priceResolver,
                $this->logger
            );

            $attributeItemType = new ObjectType([
                'name' => 'AttributeItem',
                'fields' => [
                    'id'           => ['type' => Type::nonNull(Type::string())],
                    'displayValue' => ['type' => Type::nonNull(Type::string())],
                    'value'        => ['type' => Type::nonNull(Type::string())],
                ],
            ]);

            $attributeType = new ObjectType([
                'name' => 'Attribute',
                'fields' => function () use ($attributeItemType, $attributeResolver) {
                    return [
                        'id'    => ['type' => Type::nonNull(Type::int())],
                        'name'  => ['type' => Type::nonNull(Type::string())],
                        'type'  => ['type' => Type::string()],
                        'items' => [
                            'type' => Type::listOf($attributeItemType),
                            'resolve' => fn($attribute) => $attributeResolver->resolveItemsForAttribute($attribute['id']),
                        ],
                    ];
                },
            ]);

            $orderItemType = new ObjectType([
                'name' => 'OrderItem',
                'fields' => [
                    'productId' => ['type' => Type::nonNull(Type::string())],
                    'quantity'  => ['type' => Type::nonNull(Type::int())],
                    'price'     => ['type' => Type::nonNull(Type::float())],
                ],
            ]);

            $orderType = new ObjectType([
                'name' => 'Order',
                'fields' => [
                    'id'         => ['type' => Type::nonNull(Type::int())],
                    'total'      => ['type' => Type::nonNull(Type::float())],
                    'created_at' => ['type' => Type::nonNull(Type::string())],
                    'items'      => [
                        'type' => Type::listOf($orderItemType),
                        'resolve' => fn($order) => $order['items'] ?? [],
                    ],
                ],
            ]);

            $productType = new ObjectType([
                'name' => 'Product',
                'fields' => [
                    'id'          => ['type' => Type::nonNull(Type::string())],
                    'name'        => ['type' => Type::nonNull(Type::string())],
                    'description' => ['type' => Type::string()],
                    'brand'       => ['type' => Type::string()],
                    'inStock'     => ['type' => Type::nonNull(Type::boolean())],
                    'category'    => ['type' => Type::string()],
                    'price'       => ['type' => Type::float()],
                    'gallery'     => [
                        'type' => Type::listOf(Type::string()),
                        'resolve' => fn($product) => $productResolver->resolveGallery($product['id']),
                    ],
                    'attributes'  => [
                        'type' => Type::listOf($attributeType),
                        'resolve' => fn($product) => $productResolver->resolveAttributes($product['id']),
                    ],
                ],
            ]);

            $orderInputType = new InputObjectType([
                'name' => 'OrderInput',
                'fields' => [
                    'productId' => ['type' => Type::nonNull(Type::id())],
                    'quantity'  => ['type' => Type::nonNull(Type::int())],
                    'price'     => ['type' => Type::nonNull(Type::float())],
                ],
            ]);

            $queryType = new ObjectType([
                'name' => 'Query',
                'fields' => [
                    'categories' => [
                        'type' => Type::listOf(new ObjectType([
                            'name' => 'Category',
                            'fields' => [
                                'id'   => ['type' => Type::nonNull(Type::int())],
                                'name' => ['type' => Type::nonNull(Type::string())],
                            ],
                        ])),
                        'resolve' => fn() => $categoryResolver->resolveAll(),
                    ],
                    'products' => [
                        'type' => Type::listOf($productType),
                        'args' => ['category' => ['type' => Type::string()]],
                        'resolve' => fn($_, $args) => $productResolver->resolveAll($args['category'] ?? null),
                    ],
                    'product' => [
                        'type' => $productType,
                        'args' => ['id' => ['type' => Type::nonNull(Type::id())]],
                        'resolve' => fn($_, $args) => $productResolver->resolveSingleProduct($args['id']),
                    ],
                    'orders' => [
                        'type' => Type::listOf($orderType),
                        'resolve' => fn() => $orderResolver->resolveAll(),
                    ],
                ],
            ]);

            $mutationType = new ObjectType([
                'name' => 'Mutation',
                'fields' => [
                    'createOrder' => [
                        'type' => Type::string(),
                        'args' => [
                            'products' => ['type' => Type::nonNull(Type::listOf($orderInputType))],
                        ],
                        'resolve' => fn($_, $args) => $orderResolver->createOrder($args['products']),
                    ],
                ],
            ]);

            $schema = new Schema(
                (new SchemaConfig())
                    ->setQuery($queryType)
                    ->setMutation($mutationType)
            );

            $rawInput = file_get_contents('php://input');
            $input = json_decode($rawInput, true);
            $query = $input['query'] ?? null;
            $variableValues = $input['variables'] ?? null;

            $result = GraphQLBase::executeQuery($schema, $query, null, null, $variableValues);
            $output = $result->toArray();
        } catch (Throwable $e) {
            $output = ['error' => ['message' => $e->getMessage()]];
            $this->logger->error("Error: " . $e->getMessage());
        }

        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode($output);
    }
}

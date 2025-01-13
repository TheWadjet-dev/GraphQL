<?php
require_once __DIR__ . '/vendor/autoload.php';

use GraphQL\GraphQL;
use GraphQL\Type\Schema;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

$queryType = new ObjectType([
    'name' => 'Query',
    'fields' => [
        'hola' => [
            'type' => Type::string(),
            'args' => [
                'nombre' => Type::nonNull(Type::string())
            ],
            'resolve' => function ($root, $args) {
                return '¡Hola, ' . $args['nombre'] . '!';
            }
        ],
    ],
]);

$schema = new Schema([
    'query' => $queryType
]);

try {
    $rawInput = file_get_contents('php://input');
    $input = json_decode($rawInput, true);
    $query = $input['query'] ?? '';
    $variableValues = $input['variables'] ?? null;

    $result = GraphQL::executeQuery($schema, $query, null, null, $variableValues);
    $output = $result->toArray();
} catch (\Exception $e) {
    $output = [
        'errors' => [
            ['message' => $e->getMessage()]
        ]
    ];
}

header('Content-Type: application/json');
echo json_encode($output);

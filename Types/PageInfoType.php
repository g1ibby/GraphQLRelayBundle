<?php

namespace Suribit\GraphQLRelayBundle\Types;

use GraphQL\Type\Definition\Type;
use Suribit\GraphQLBundle\Support\Type as GraphQLType;

class PageInfoType extends GraphQLType
{
    /**
     * Attributes of PageInfo.
     *
     * @var array
     */
    protected $attributes = [
        'name' => 'PageInfo',
        'description' => 'Information about pagination in a connection.'
    ];

    /**
     * Fields available on PageInfo.
     *
     * @return array
     */
    public function fields()
    {
        return [
            'hasNextPage' => [
                'type' => Type::nonNull(Type::boolean()),
                'description' => 'When paginating forwards, are there more items?',
                'resolve' => function ($collection, $test) {
                    if ($collection['total'] - ($collection['first'] * $collection['currentPage']) > 0) {
                        return true;
                    }
                    return false;
                }
            ],
            'hasPreviousPage' => [
                'type' => Type::nonNull(Type::boolean()),
                'description' => 'When paginating backwards, are there more items?',
                'resolve' => function ($collection) {
                    if ($collection['currentPage'] > 1) {
                        return true;
                    }
                    return false;
                }
            ]
        ];
    }
}
<?php

namespace Suribit\GraphQLRelayBundle\Node;

use GraphQL\Type\Definition\Type;
use Suribit\GraphQLBundle\Support\InterfaceType;

class NodeType extends InterfaceType
{
    /**
     * Interface attributes.
     *
     * return array
     */
    public function attributes()
    {
        return [
            'name' => 'Node',
            'description' => 'An object with an ID.'
        ];
    }

    /**
     * Available fields on type.
     *
     * @return array
     */
    public function fields()
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::id()),
                'description' => 'The id of the object.'
            ]
        ];
    }

    /**
     * Resolve the interface.
     *
     * @param  mixed $obj
     * @return mixed
     */
    public function resolveType($obj)
    {
        if (is_array($obj)) {
            return $this->manager->type($obj['graphqlType'])->original;
        }

        return $this->manager->type($obj->graphqlType)->original;
    }
}
<?php

namespace Suribit\GraphQLRelayBundle\Node;

use GraphQL\Type\Definition\Type;
use GraphQL\Type\Definition\ResolveInfo;
use Suribit\GraphQLBundle\Support\Query;
use Suribit\GraphQLRelayBundle\GlobalIdTrait;

class NodeQuery extends Query
{
    use GlobalIdTrait;

    /**
     * Associated GraphQL Type.
     *
     * @return mixed
     */
    public function type()
    {
        return $this->manager->type('node')->original;
    }

    /**
     * Arguments available on node query.
     *
     * @return array
     */
    public function args()
    {
        return [
            'id' => [
                'name' => 'id',
                'type' => Type::nonNull(Type::id())
            ]
        ];
    }

    /**
     * Resolve query.
     *
     * @param  string $root
     * @param  array $args
     * @param ResolveInfo $info
     * @return mixed
     */
    public function resolve($root, array $args, ResolveInfo $info)
    {
        return $this->getModel($args);
    }

    /**
     * Get associated model.
     *
     * @param  array $args
     * @return mixed
     */
    protected function getModel(array $args)
    {
        // Here, we decode the base64 id and get the id of the type
        // as well as the type's name.
        list($typeClass, $id) = $this->decodeGlobalId($args['id']);

        $objectType = $this->manager->type($typeClass);
        $model = $objectType->resolveById($id);
        if (is_array($model)) {
            $model['graphqlType'] = $typeClass;
        } elseif (is_object($model)) {
            $model->graphqlType = $typeClass;
        }

        return $model;
    }
}

<?php

namespace Suribit\GraphQLRelayBundle\Mutations;

use GraphQL\Type\Definition\Type;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\InputObjectType;
use Suribit\GraphQLBundle\Support\Mutation;
use Suribit\GraphQLRelayBundle\GlobalIdTrait;

abstract class MutationWithClientId extends Mutation
{
    use GlobalIdTrait;

    /**
     * Type being mutated is RelayType.
     *
     * @var boolean
     */
    protected $mutatesRelayType = true;

    /**
     * Generate Relay compliant output type.
     *
     * @return InputObjectType
     */
    public function type()
    {
        return new ObjectType([
            'name' => ucfirst($this->name()) . 'Payload',
            'fields' => array_merge($this->outputFields(), [
                'clientMutationId' => [
                    'type' => Type::nonNull(Type::string())
                ]
            ])
        ]);
    }

    /**
     * Generate Relay compliant arguments.
     *
     * @return array
     */
    public function args()
    {
        $inputType = new InputObjectType([
            'name' => ucfirst($this->name()) . 'Input',
            'fields' => array_merge($this->inputFields(), [
                'clientMutationId' => [
                    'type' => Type::nonNull(Type::string())
                ]
            ])
        ]);

        return [
            'input' => [
                'type' => Type::nonNull($inputType)
            ]
        ];
    }

    /**
     * Resolve mutation.
     *
     * @param  mixed       $_
     * @param  array       $args
     * @param  ResolveInfo $info
     * @return array
     */
    public function resolve($_, $args, ResolveInfo $info)
    {
        if ($this->mutatesRelayType && isset($args['input']['id'])) {
            $args['input']['id'] = $this->decodeRelayId($args['input']['id']);
        }

        $payload = $this->mutateAndGetPayload($args['input'], $info);

        return array_merge($payload, [
            'clientMutationId' => $args['input']['clientMutationId']
        ]);
    }

    /**
     * Get resolver for relay mutation.
     *
     * @return mixed
     */
    protected function getResolver()
    {
        if (!method_exists($this, 'resolve')) {
            return null;
        }

        $resolver = array($this, 'resolve');

        return function () use ($resolver) {
            $arguments = func_get_args();

            return call_user_func_array($resolver, $arguments);
        };
    }

    /**
     * Perform mutation.
     *
     * @param  array       $input
     * @param  ResolveInfo $info
     * @return array
     */
    abstract protected function mutateAndGetPayload(array $input, ResolveInfo $info);

    /**
     * List of available input fields.
     *
     * @return array
     */
    abstract protected function inputFields();

    /**
     * List of output fields.
     *
     * @return array
     */
    abstract protected function outputFields();

    /**
     * Get name of mutation.
     *
     * @return string
     */
    abstract protected function name();
}

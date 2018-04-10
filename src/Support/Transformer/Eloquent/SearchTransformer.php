<?php

namespace StudioNet\GraphQL\Support\Transformer\Eloquent;

use StudioNet\GraphQL\Definition\Type;
use StudioNet\GraphQL\Support\Definition\Definition;
use StudioNet\GraphQL\Support\Transformer\Transformer;

class SearchTransformer extends Transformer
{
    /**
     * Return query name
     *
     * @param  Definition $definition
     * @return string
     */
    public function getName(Definition $definition) {
        return sprintf('search%s', ucfirst(strtolower(str_plural($definition->getName()))));
    }

    /**
     * Resolve type
     *
     * @param  Definition $definition
     *
     * @return \GraphQL\Type\Definition\ObjectType|\GraphQL\Type\Definition\ListOfType
     */
    public function resolveType(Definition $definition)
    {
        return Type::listOf($definition->resolveType());
    }

    /**
     * {@inheritDoc}
     *
     * @param  Definition $definition
     * @return array
     */
    public function getArguments(Definition $definition)
    {
        return [
            'query' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'Search terms'
            ],
            'index' => [
                'type' => Type::string(),
                'description' => 'The index to search'
            ]
        ];
    }

    /**
     * Return fetchable node resolver
     *
     * @param  array $opts
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getResolver(array $opts)
    {
        $builder = call_user_func(get_class($opts['source']).'::search', $opts['args']['query']);

        foreach ($opts['args'] as $key => $value) {
            switch ($key) {
                case 'index':
                    $builder->within($value);
                    break;

            }
        }

        return $builder->get();
    }
}

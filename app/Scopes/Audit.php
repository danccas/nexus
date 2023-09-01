<?php

namespace App\Scopes;

use Core\Concerns\Scope;
use Core\Database\Builder;
use Core\Model;

class MultiTenantScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $builder
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return void
		 */
    public function apply(Builder $builder, Model $model)
		{
      $builder->where('tenant_id', '=', user()->tenant_id);
    }
}

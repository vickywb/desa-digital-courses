<?php

namespace App\Repository;

use App\Models\User;

class UserRepository
{
    public function __construct(private User $user) {}

    public function get(array $params = [], ?int $perPage = null)
    {
        $query = $this->buildQuery($params);

        return $perPage ? $query->paginate($perPage) : $query->get();
    }

    public function buildQuery(array $params)
    {
        $query = $this->user->newQuery();

        // Menghindari N+1 Problem, jadi menggunakan eager loading
        if (!empty($params['with'])) {
            $query->with($params['with']);
        }

        // Exact filters (DRY dengan loop) 
        $exactFilters = ['email', 'role', 'username'];
        
        foreach ($exactFilters as $field) {
            if (!empty($params[$field])) {
                $query->where($field, $params[$field]);
            }
        }

        // General Filter
        if (!empty($params['search'])) {
            $query->where(function ($q) use ($params) {
                $q->where('name', 'like', "%{$params['search']}%")
                  ->orWhere('email', 'like', "%{$params['search']}%");
            });
        }

        // Sorting
        $sortBy = $params['sort_by'] ?? 'created_at';
        $sortOrder = $params['sort_order'] ?? 'desc';
        
        $query->orderBy($sortBy, $sortOrder);

        return $query;
    }

    public function save(User $user): User
    {
        $user->save();
        
        return $user;
    }
}
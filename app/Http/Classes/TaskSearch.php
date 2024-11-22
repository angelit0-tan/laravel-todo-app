<?php

namespace App\Http\Classes;

use DB;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

// This class will search thru the task model
class TaskSearch {

    protected $request;
    protected $builder;

    public function __construct(Request $request) {
        $this->request = $request;
    }

    /**
     * Will check if method exists from the request, and will call each of the request as function / method
     *
     * @param  Builder $builder
     *
     * @return Builder
     */
    public function apply(Builder $builder) {
        $this->builder = $builder;
        foreach($this->searches() as $name => $value) {
            if (method_exists($this, Str::camel($name))) {
                // Call the function and pass the value as parameter
                call_user_func_array([$this, Str::camel($name)], collect([$value])->toArray());
            }
        }

        return $this->builder;
    }

    /**
     * Get all the request
     *
     * @return Request
     */
    public function searches() {
        return $this->request->all();
    }

    /**
     * Query title
     *
     * @param $value
     *
     * @return Builder
     */
    public function title($value = null) {
        $title = "%$value%";
        if ($value) return $this->builder->whereRaw("title LIKE ?", [$title]);
    }

    /**
     * Query description
     *
     * @param $value
     *
     * @return Builder
     */
    public function description($value = null) {
        $description = "%$value%";
        if ($value) return $this->builder->whereRaw("description LIKE ?", [$description]);
    }


    /**
     * Query priority task
     *
     * @param $value
     *
     * @return Builder
     */
    public function priority($value) {
        if ($value) return $this->builder->whereRaw("task_priority = ?", [$value]);
    }

    
    /**
     * Query completed from
     *
     * @param $value
     *
     * @return Builder
     */
    public function completedDateFrom($date) {
        if ($date) {
            return $this->builder->whereDate('completed_at', '>=',  $date ? Carbon::parse($date)->format('Y-m-d') : Carbon::now()->format('Y-m-d'));
        }
    }

    /**
     * Query completed to
     *
     * @param $value
     *
     * @return Builder
     */
    public function completedDateTo($date) {
        if ($date) {
            return $this->builder->whereDate('completed_at', '<=',  $date ? Carbon::parse($date)->format('Y-m-d') : Carbon::now()->format('Y-m-d'));
        }
    }

    /**
     * Query due date from
     *
     * @param $value
     *
     * @return Builder
     */
    public function dueDateFrom($date) {
        if ($date) {
            return $this->builder->whereDate('due_date', '>=',  $date ? Carbon::parse($date)->format('Y-m-d') : Carbon::now()->format('Y-m-d'));
        }
    }

    /**
     * Query due date to
     *
     * @param $value
     *
     * @return Builder
     */
    public function dueDateTo($date) {
        if ($date) {
            return $this->builder->whereDate('due_date', '<=',  $date ? Carbon::parse($date)->format('Y-m-d') : Carbon::now()->format('Y-m-d'));
        }
    }

    /**
     * Query archive date from
     *
     * @param $value
     *
     * @return Builder
     */
    public function archiveDateFrom($date) {
        if ($date) {
            return $this->builder->whereDate('archived_at', '>=',  $date ? Carbon::parse($date)->format('Y-m-d') : Carbon::now()->format('Y-m-d'));
        }
    }

    /**
     * Query archive date to
     *
     * @param $value
     *
     * @return Builder
     */
    public function archiveDateTo($date) {
        if ($date) {
            return $this->builder->whereDate('archived_at', '<=',  $date ? Carbon::parse($date)->format('Y-m-d') : Carbon::now()->format('Y-m-d'));
        }
    }
}
<?php

// flash message to session [class, message]
if (!function_exists('flash')) {
    function flash($data = [])
    {
        session()->flash('flash', $data);
    }
}

// create activity log
if (!function_exists('activity')) {
    function activity($message, $data = [], $model = null)
    {
        // unset hidden form fields
        foreach (['_token', '_method', '_submit'] as $unset_key) {
            if (isset($data[$unset_key])) {
                unset($data[$unset_key]);
            }
        }

        // create model
        app(config('lap.models.activity_log'))->create([
            'user_id' => auth()->check() ? auth()->user()->id : null,
            'model_id' => $model ? $model->id : null,
            'model_class' => $model ? get_class($model) : null,
            'message' => $message,
            'data' => $data ? $data : null,
        ]);
    }
}
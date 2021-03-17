<?php

namespace Tests\Traits;

use Lang;
use Illuminate\Http\Response;

trait TestInvalidation
{

    abstract protected function routeStore();

    private function assertRegisterInvalidation($sendData, $rule, $field, $ruleParams = [])
    {
        $response = $this->json('POST', $this->routeStore(), $sendData);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        $fieldName = str_replace('_', ' ', $field);
        $response->assertJsonFragment([
            $field => [Lang::get("validation.{$rule}", ['attribute' => $fieldName] + $ruleParams)]
        ]);
    }
}

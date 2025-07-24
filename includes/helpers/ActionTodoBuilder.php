<?php
namespace WPBackendDash\Helpers;

class ActionTodoBuilder
{
    protected array $actions = [];

    /**
     * Agrega una acción a la lista
     *
     * @param string $methodRef Nombre de función o Clase.Método (ej. "MyClass.greet" o "test")
     * @param array|string|int|float|null $params Parámetros a enviar al método
     * @return $this
     */
    public function add(string $methodRef, array|string|int|float|null $params = []): self
    {
        $this->actions[] = [$methodRef, $params];
        return $this;
    }

    /**
     * Devuelve el array final para enviar en la respuesta
     *
     * @return array
     */
    public function make(): array
    {
        return $this->actions;
    }

    /**
     * Devuelve la estructura como parte de una respuesta WordPress (ej. wp_send_json)
     *
     * @param array $extraData Datos adicionales para combinar con el array
     * @return array
     */
    public function response(array $extraData = []): array
    {
        return array_merge($extraData, [
            'actiontodo' => $this->make(),
        ]);
    }

    /**
     * Limpia todas las acciones
     *
     * @return $this
     */
    public function reset(): self
    {
        $this->actions = [];
        return $this;
    }
}

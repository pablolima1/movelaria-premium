<?php

namespace App\Services;

use App\Models\Cliente;
use App\Repositories\ClienteRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ClienteService
{
    public function __construct(private readonly ClienteRepository $repository)
    {
    }

    public function list(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        return $this->repository->paginate($filters, $perPage);
    }

    public function create(array $data): Cliente
    {
        return $this->repository->create($this->normalize($data));
    }

    public function update(Cliente $cliente, array $data): Cliente
    {
        return $this->repository->update($cliente, $this->normalize($data));
    }

    public function delete(Cliente $cliente): void
    {
        $this->repository->delete($cliente);
    }

    private function normalize(array $data): array
    {
        foreach (['cpf_cnpj', 'telefone'] as $field) {
            if (array_key_exists($field, $data) && $data[$field] !== null) {
                $digits = preg_replace('/\D+/', '', (string) $data[$field]);
                $data[$field] = $digits === '' ? null : $digits;
            }
        }

        foreach (['cpf_cnpj', 'telefone', 'email', 'endereco', 'observacoes'] as $field) {
            if (array_key_exists($field, $data) && $data[$field] === '') {
                $data[$field] = null;
            }
        }

        if (isset($data['nome'])) {
            $data['nome'] = trim((string) $data['nome']);
        }

        return $data;
    }
}

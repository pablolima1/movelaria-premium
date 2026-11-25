<?php

namespace App\Services;

use App\Models\AliquotaImposto;
use App\Repositories\AliquotaImpostoRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class AliquotaImpostoService
{
    public function __construct(private readonly AliquotaImpostoRepository $repository)
    {
    }

    public function list(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        return $this->repository->paginate($filters, $perPage);
    }

    public function vigente(): ?AliquotaImposto
    {
        return $this->repository->vigente();
    }

    public function create(array $data): AliquotaImposto
    {
        return $this->repository->create($this->normalize($data));
    }

    public function update(AliquotaImposto $aliquota, array $data): AliquotaImposto
    {
        return $this->repository->update($aliquota, $this->normalize($data));
    }

    public function delete(AliquotaImposto $aliquota): void
    {
        $this->repository->delete($aliquota);
    }

    private function normalize(array $data): array
    {
        if (array_key_exists('vigente_ate', $data) && $data['vigente_ate'] === '') {
            $data['vigente_ate'] = null;
        }

        if (isset($data['percentual'])) {
            $data['percentual'] = round((float) $data['percentual'], 2);
        }

        return $data;
    }
}

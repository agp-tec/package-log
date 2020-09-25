<?php
/**
 *
 * Data e hora: 2020-09-23 09:39:59
 * Controller/Api gerada automaticamente
 *
 */


namespace Agp\Log\Controller\Api;


use Agp\Modelo\Controller\Controller;
use Agp\Modelo\Model\Entity\Pais;
use Agp\Modelo\Model\Resource\PaisResource;
use Facades\Agp\Modelo\Model\Repository\PaisRepository;
use Facades\Agp\Modelo\Model\Service\PaisService;
use Illuminate\Http\Request;


class PaisController extends Controller
{
    public function index()
    {
        return PaisResource::collection(PaisRepository::getList());
    }
    public function store(Request $request, Pais $pais)
    {
        $this->validate($request, $pais->getRules());
        $pais->sync($request->all());
        PaisService::store($pais);
        return new PaisResource($pais);
    }

    public function update(Request $request, Pais $pais)
    {
        $this->validate($request, $pais->getRules());
        $pais->sync($request->all());
        PaisService::update($pais);
        return new PaisResource($pais);
    }

    public function destroy(Pais $pais)
    {
        PaisService::destroy($pais);
        return response()->json();
    }
}

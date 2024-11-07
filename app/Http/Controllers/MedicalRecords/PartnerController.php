<?php

namespace App\Http\Controllers\MedicalRecords;

use App\Http\Controllers\Controller;
use App\Models\MedicalRecords\Partner;
use App\Core\Request;
use App\Core\DB;

class PartnerController extends Controller
{
    // Método para mostrar la vista de crear una nueva pareja
    public function create()
    {
        echo $this->blade->make('medicalRecords.pareja.create'); exit; 
    }

    // Método para guardar una nueva pareja
    public function store(Request $request)
    {
        $data = $request->all();
        
        if ($request->input('boton_datos') == "GUARDAR DATOS") {
            $this->insertPareja($data);
        }

        if ($request->input('graba_nota') == 'GRABAR') {
            $this->updatePacienteNota($data);
        }

        //return redirect()->route('partner.index');
    }

    // Método para listar todas las parejas
    public function index()
    {
        $partners = Partner::all();
        echo $this->blade->make('medicalRecords.pareja.index', compact('partners')); exit;  
    }

    // Método para mostrar una pareja específica
    public function show($id)
    {
        $partner = Partner::find($id);
        echo $this->blade->make('medicalRecords.pareja.show', compact('partners')); exit;  
    }

    // Método para editar una pareja
    public function edit($id)
    {
        $partner = Partner::find($id);
        echo $this->blade->make('medicalRecords.pareja.edit', compact('partners')); exit;   
    }

    // Método para actualizar una pareja
    public function update(Request $request, $id)
    {
        $data = $request->all();
        $partner = Partner::find($id);
        $partner->update($data);

        //return redirect()->route('partner.index');
    }

    // Método para eliminar una pareja
    public function destroy($id)
    {
        $partner = Partner::find($id);
        $partner->delete();

        //return redirect()->route('partner.index');
    }

    // Método para insertar una nueva pareja
    protected function insertPareja($data)
    {
        $partner = Partner::where('p_dni', $data['p_dni'])->where('estado', 1)->first();
        if (!$partner) {
            $newPartner = Partner::create([
                'p_dni' => $data['p_dni'],
                'p_tip' => $data['p_tip'],
                'p_nom' => $data['p_nom'],
                'p_ape' => $data['p_ape'],
                'p_fnac' => $data['p_fnac'],
                'p_tcel' => $data['p_tcel'],
                'p_tcas' => $data['p_tcas'],
                'p_tofi' => $data['p_tofi'],
                'p_mai' => $data['p_mai'],
                'p_dir' => $data['p_dir'],
                'p_prof' => $data['p_prof'],
                'p_san' => $data['p_san'],
                'p_raz' => $data['p_raz'],
                'p_med' => $data['m_tratante'],
                'idusercreate' => $_SESSION['login'],
                'tipo_clienteid' => $data['don'],
                'programaid' => $data['medios_comunicacion_id'],
                'sedeid' => $data['sede']
            ]);
            // Insert into hc_pare_paci
            DB::table('hc_pare_paci')->insert([
                'dni' => $data['dni'],
                'p_dni' => $data['p_dni']
            ]);
        } else {
            echo "<script>alert('{$partner->p_nom} {$partner->p_ape} YA EXISTE EN EL SISTEMA!');</script>";
        }
    }

    // Método para actualizar la nota de un paciente
    protected function updatePacienteNota($data)
    {
        $hora_actual = date("Y-m-d H:i:s");
        DB::table('hc_paciente')
            ->where('dni', $data['dni'])
            ->update([
                'nota' => $data['nota'],
                'iduserupdate' => $_SESSION['login'],
                'updatex' => $hora_actual
            ]);

        DB::table('appinmater_log.hc_paciente')->insert(
            DB::table('hc_paciente')
                ->where('dni', $data['dni'])
                ->select('*', DB::raw("'U' as action"))
                ->first()
        );
    }
}

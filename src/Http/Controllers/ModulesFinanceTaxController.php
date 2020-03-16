<?php

namespace Dorcas\ModulesFinanceTax\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Dorcas\ModulesFinanceTax\Models\TaxAuthorities;
use App\Dorcas\Hub\Utilities\UiResponse\UiResponse;
use App\Http\Controllers\HomeController;
use Hostville\Dorcas\Sdk;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
use League\Csv\Reader;
use Hostville\Dorcas\LaravelCompat;

class ModulesFinanceTaxController extends Controller
{
    public  function __construct()
    {
        parent::__construct();
        $this->data = [
            'page' => ['title' => config('modules-finance.title')],
            'header' => ['title' => config('modules-finance.title')],
            'selectedMenu' => 'modules-finance',
            'submenuConfig' => 'navigation-menu.modules-finance.sub-menu',
            'submenuAction' => ''
        ];
    }

    // Commenting out index page for tax sub menu
    public function index(Request $request, Sdk $sdk){
        try{
            $this->data['page']['title'] .= ' &rsaquo; Tax';
            $this->data['header']['title'] = 'Finance Tax';
            $this->data['selectedSubMenu'] = 'tax-main';
            $this->data['submenuAction'] = '';

            $this->setViewUiResponse($request);

            return view('modules-finance-tax::Tax/index',$this->data);
        }catch (\Exception $e){
            $this->setViewUiResponse($request);
            return view('modules-finance-tax::Tax/index',$this->data);
        }

    }

    public function Authorities(Request $request, Sdk $sdk){
        try {
            $this->data['page']['title'] .= ' &rsaquo; Tax Authority';
            $this->data['header']['title'] = 'Finance Tax Authority';
            $this->data['submenuAction'] = '';
            $this->setViewUiResponse($request);
            $this->data['args'] = $request->query->all();
            $this->data['tax_authorities'] = $this->getFinanceTaxAuthorities();
            switch ($this->data){
                case !empty($this->data['tax_authorities']):
                    $this->data['submenuAction'] .= '
                    <div class="dropdown"><button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown">Actions</button>
                            <div class="dropdown-menu">
                          <a href="#" data-toggle="modal" data-target="#tax-authorities-add-modal" class="dropdown-item">Add Tax Authority</a>
                          </div>
                          </div>';

            }
            return view('modules-finance-tax::Tax/tax_authority', $this->data);

        }
        catch (\Exception $e){
            return view('modules-finance-tax::Tax/tax_authority',$this->data);
        }

    }

    /**
     * @param Request $request
     * @param Sdk     $sdk
     *
     * @return \Illuminate\Http\JsonResponse
     */

    public function searchAuthority(Request $request, Sdk $sdk)
    {

        $search = $request->query('search', '');
        $offset = (int) $request->query('offset', 0);
        $limit = (int) $request->query('limit', 10);

        # get the request parameters
        $path = ['authority'];

        $query = $sdk->createTaxResource();
        $query = $query->addQueryArgument('limit', $limit)
            ->addQueryArgument('page', get_page_number($offset, $limit));
        if (!empty($search)) {
            $query = $query->addQueryArgument('search', $search);
        }
        $response = $query->send('get', $path);
        # make the request
        if (!$response->isSuccessful()) {
            // do something here
            throw new RecordNotFoundException($response->errors[0]['title'] ?? 'Could not find any matching authorities.');
        }
        $this->data['total'] = $response->meta['pagination']['total'] ?? 0;
        # set the total
        $this->data['rows'] = $response->data;
        # set the data
        return response()->json($this->data);
    }

    public function createAuthority(Request $request ,Sdk $sdk){
        try{
            $resource = $sdk->createTaxResource();
            $resource = $resource->addBodyParam('authority_name',$request->authority_name)
                ->addBodyParam('payment_mode',$request->payment_type)
                ->addBodyParam('payment_details',$request->fields)
                ->addBodyParam('default_payment_details',$request->default_fields);
            $response = $resource->send('post',['authority']);
            if (!$response->isSuccessful()) {
                $message = $response->errors[0]['title'] ?? '';
                throw new \RuntimeException('Failed while adding the Tax Authority '.$message);

            }
            return response()->json(['message'=>'Tax Authority Created Successfully'],200);

        }
        catch (\Exception $e){
            return response()->json(['message'=>$e->getMessage()],400);


        }
    }

    public function updateAuthority(Request $request, Sdk $sdk, string $id)
    {
        try {
            $resource = $sdk->createTaxResource();
            $resource = $resource->addBodyParam('authority_name', $request->authority_name)
                ->addBodyParam('payment_mode', $request->payment_mode)
                ->addBodyParam('payment_details', json_encode($request->fields))
                ->addBodyParam('default_payment_details', json_encode($request->default_fields));
            $response = $resource->send('put', ['authority',$id]);
            if (!$response->isSuccessful()) {
                $message = $response->errors[0]['title'] ?? '';
                throw new \RuntimeException('Failed while updating the Tax Authority ' . $message);

            }
            return response()->json(['message' => 'Tax Authority Updated Successfully'], 200);

        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    public function deleteAuthority(Request $request, Sdk $sdk, string $id){
        try{
            $resource = $sdk->createTaxResource();
            $response = $resource->send('delete', ['authority',$id]);
            if (!$response->isSuccessful()) {
                throw new \RuntimeException($response->errors[0]['title'] ?? 'Failed while deleting the authority.');

            }
            $this->data = $response->getData();
            return response()->json($this->data);

        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    public function singleAuthority(Request $request , Sdk $sdk, string $id){
        try {
            $this->data['page']['title'] .= ' &rsaquo; Tax Authority';
            $this->data['header']['title'] = 'Finance Tax Authority';
            $this->data['submenuAction'] = '';
            $this->data['args'] = $request->query->all();
            $this->setViewUiResponse($request);
            $response = $sdk->createTaxResource()->send('get',['authority',$id]);

            if(!$response->isSuccessful()){

                $response = (tabler_ui_html_response(['Could not find the tax authority']))->setType(UiResponse::TYPE_ERROR);
                return redirect(url()->route('tax-authorities'))->with('UiResponse', $response);
            }
            $authority = $response->getData(true);
            $elements = $this->getElements($request,$sdk,$id);
            $accounts = $this->getAccounts($request,$sdk);
            $this->data['authority'] = $authority;
            $this->data['elements'] = $elements->getData(true);
            $this->data['accounts'] = $accounts->getData(true);
            return view('modules-finance-tax::Tax/single', $this->data);

        }
        catch (\Exception $e){
            return view('modules-finance-tax::Tax/single',$this->data);
        }
    }

    public function searchElement(Request $request, Sdk $sdk){
        $search = $request->query('search', '');
        $id = $request->query('id','');
        $offset = (int) $request->query('offset', 0);
        $limit = (int) $request->query('limit', 10);

        # get the request parameters
        $path = ['element'];

        $query = $sdk->createTaxResource();
        $query = $query->addQueryArgument('limit', $limit)
            ->addQueryArgument('page', get_page_number($offset, $limit))
            ->addQueryArgument('id', $id);
        if (!empty($search)) {
            $query = $query->addQueryArgument('search', $search);
        }
        $response = $query->send('get', $path);
        # make the request
        if (!$response->isSuccessful()) {
            // do something here
            throw new RecordNotFoundException($response->errors[0]['title'] ?? 'Could not find any matching elements.');
        }
        $this->data['total'] = $response->meta['pagination']['total'] ?? 0;
        # set the total
        $this->data['rows'] = $response->data;
        # set the data
        return response()->json($this->data);
    }

    public function addElement(Request $request, Sdk $sdk){
        try{
            $resource = $sdk->createTaxResource();
            $resource = $resource->addBodyParam('authority',$request->authority)
                ->addBodyParam('element_type',$request->element_type)
                ->addBodyParam('element_name',$request->element_name)
                ->addBodyParam('type_data',$request->type_data)
                ->addBodyParam('frequency',$request->frequency)
                ->addBodyParam('target_accounts',$request->accounts)
                ->addBodyParam('frequency_year',$request->frequency_year)
                ->addBodyParam('frequency_month',$request->frequency_month);
            $response = $resource->send('post',['element']);
//            return $response->errors;
            if (!$response->isSuccessful()) {
                $message = $response->errors[0]['title'] ?? '';
                throw new \RuntimeException('Failed while adding the Tax Element '.$message);

            }
            return response()->json(['message'=>'Tax Element Created Successfully'],200);

        }
        catch (\Exception $e){
            return response()->json(['message'=>$e->getMessage()],400);


        }
    }

    private function getElements(Request $request,Sdk $sdk, string $id){

        $offset = (int) $request->query('offset', 10);

        $limit = (int) $request->query('limit', 100);

        # get the request parameters

        $query = $sdk->createTaxResource();
        $query = $query->addQueryArgument('limit', $limit)
            ->addQueryArgument('page', get_page_number($offset, $limit));
        $response = $query->send('get',['elements']);
        return $response;
    }

    private function getAccounts(Request $request,Sdk $sdk){
        $offset = (int) $request->query('offset', 0);
        $limit = (int) $request->query('limit', 10);

        $query = $sdk->createFinanceResource();
        $query = $query->addQueryArgument('limit', $limit)
            ->addQueryArgument('page', get_page_number($offset, $limit));
        $response = $query->send('get',['accounts']);
        return $response;
    }

    public function singleElement(Request $request , Sdk $sdk, string $id){
        try {

            $this->setViewUiResponse($request);
            $response = $sdk->createTaxResource()->send('get',['element',$id]);

            if(!$response->isSuccessful()){
                $message = $response->errors[0]['title'] ?? '';
                throw new \RuntimeException($message);
            }
            $element = $response->getData(true);
            //this gets already add target accounts
            $element->accounts_name  =  collect($this->getAccounts($request,$sdk)->getData(true))->whereNotIn('id',$element->target_accounts)->toArray();
            //this gets remaining target accounts
            $element->target_accounts_name  = collect($this->getAccounts($request,$sdk)->getData(true))->whereIn('id',$element->target_accounts)->toArray();
            return response()->json([$element],200);

        }
        catch (\Exception $e){
            return response()->json(['message'=>$e->getMessage()],400);

        }
    }

    public function updateElement(Request $request, Sdk $sdk, string $id){
        try {
            $resource = $sdk->createTaxResource();
            $resource = $resource->addBodyParam('authority',$request->authority)
                ->addBodyParam('element_type',$request->element_type)
                ->addBodyParam('element_name',$request->element_name)
                ->addBodyParam('type_data',json_encode($request->type_data))
                ->addBodyParam('frequency',$request->frequency)
                ->addBodyParam('target_account', (array) $request->target_accounts)
                ->addBodyParam('frequency_year',$request->frequency_year)
                ->addBodyParam('frequency_month',$request->frequency_month);
            $response = $resource->send('put', ['element',$id]);
            if (!$response->isSuccessful()) {
                $message = $response->errors[0]['title'] ?? '';
                throw new \RuntimeException('Failed while updating the Tax Element ' . $message);

            }
            return response()->json(['message' => 'Tax Element Updated Successfully'], 200);

        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    public function deleteElement(Request $request, Sdk $sdk, string $id){
        try{
            $resource = $sdk->createTaxResource();
            $response = $resource->send('delete', ['element',$id]);
            if (!$response->isSuccessful()) {
                throw new \RuntimeException($response->errors[0]['title'] ?? 'Failed while deleting the element.');

            }
            $this->data = $response->getData();
            return response()->json($this->data);

        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }



    public function Runs(Request $request, Sdk $sdk, $id){
        try {
            $this->data['page']['title'] .= ' &rsaquo; Tax Run';
            $this->data['header']['title'] = 'Finance Tax Run';
            $this->data['submenuAction'] .= '
                    <div class="dropdown"><button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown">Actions</button>
                            <div class="dropdown-menu">
                          <a href="#" data-toggle="modal" data-target="#tax-run-add-modal" class="dropdown-item">Add Tax Run</a>
                          </div>
                          </div>';
            $this->setViewUiResponse($request);
            $this->data['args'] = $request->query->all();
            $response = $this->singleElement($request,$sdk,$id);
            if(!$response->isSuccessful()){
                $response = (tabler_ui_html_response(['Could not find the tax element']))->setType(UiResponse::TYPE_ERROR);
                return redirect(url()->previous())->with('UiResponse', $response);
            }
            $this->data['element'] = $response->getData(true);
            return view('modules-finance-tax::Tax/tax_run', $this->data);

        }
        catch (\Exception $e){
            return view('modules-finance-tax::Tax/index',$this->data);
        }
    }

    public function createRun(Request $request, Sdk $sdk){
        try{
            $resource = $sdk->createTaxResource();
            $resource = $resource
                ->addBodyParam('element',$request->selected_element)
                ->addBodyParam('run_name',$request->run_name);
            $response = $resource->send('post',['run']);

            if (!$response->isSuccessful()) {
                $message = $response->errors[0]['title'] ?? '';
                throw new \RuntimeException('Failed while adding the Tax Run '.$message);

            }
            return response()->json(['message'=>'Tax Run Created Successfully'],200);

        }
        catch (\Exception $e){
            return response()->json(['message'=>$e->getMessage()],400);


        }
    }

    public function searchRun(Request $request, Sdk $sdk){
        $search = $request->query('search', '');
        $id = $request->query('id','');
        $offset = (int) $request->query('offset', 0);
        $limit = (int) $request->query('limit', 10);

        # get the request parameters
        $path = ['run'];

        $query = $sdk->createTaxResource();
        $query = $query->addQueryArgument('limit', $limit)
            ->addQueryArgument('page', get_page_number($offset, $limit))
            ->addQueryArgument('id', $id);
        if (!empty($search)) {
            $query = $query->addQueryArgument('search', $search);
        }
        $response = $query->send('get', $path);
        # make the request
        if (!$response->isSuccessful()) {
            // do something here
            throw new RecordNotFoundException($response->errors[0]['title'] ?? 'Could not find any matching tax runs.');
        }
        $this->data['total'] = $response->meta['pagination']['total'] ?? 0;
        # set the total
        $this->data['rows'] = $response->data;
        # set the data
        return response()->json($this->data);
    }

    public function updateRun(Request $request, Sdk $sdk, $id){
        try {
            $resource = $sdk->createTaxResource();
            $resource = $resource
                ->addBodyParam('element',$request->selected_element)
                ->addBodyParam('run_name',$request->run_name)
                ->addBodyParam('isActive',$request->status);
            $response = $resource->send('put', ['run',$id]);
            if (!$response->isSuccessful()) {
                $message = $response->errors[0]['title'] ?? '';
                throw new \RuntimeException('Failed while updating the Tax Run ' . $message);

            }
            return response()->json(['message' => 'Tax Run Updated Successfully'], 200);

        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }
}

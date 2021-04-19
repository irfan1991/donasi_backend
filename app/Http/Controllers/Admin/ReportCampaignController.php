<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ReportCampaign;
use App\Models\Campaign;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ReportCampaignController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $reportCampaigns = ReportCampaign::latest()->when(request()->q, function($reportCampaigns) {
            $reportCampaigns = $reportCampaigns->where('title', 'like', '%'. request()->q . '%');
        })->paginate(10);

        return view('admin.report-campaign.index', compact('reportCampaigns'));
    }

    public function getReportCampaigns($id)
    {
        $reportCampaigns = ReportCampaign::with('campaigns')->where('campaign_id',$id)->latest()->when(request()->q, function($reportCampaigns) {
            $reportCampaigns = $reportCampaigns->where('title', 'like', '%'. request()->q . '%');
        })->paginate(10);

       // var_dump($reportCampaigns);die();

        return view('admin.report-campaign.index', compact('reportCampaigns','id'));
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function createReport($id)
    {
        //$campaigns = Campaign::latest()->get();
        return view('admin.report-campaign.create', compact('id'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'image'             => 'image|mimes:png,jpg,jpeg',
            'title'             => 'required',
            'campaign_id'       => 'required',
            'current_donation'   => 'required|numeric',
            'current_date'          => 'required',
            'description'       => 'required'
        ]);

        $image ='';
        //check jika image kosong
        if($request->file('image') == '') {
            //upload image
            $imageAdd = $request->file('image');
            $imageAdd->storeAs('public/reportcampaigns', $imageAdd->hashName());
            $image = $imageAdd->hashName();
        }

        $reportCampaign = ReportCampaign::create([
            'title'             => $request->title,
            'campaign_id'       => $request->campaign_id,
            'current_donation'  => $request->current_donation,
            'current_date'      => $request->current_date,
            'description'       => $request->description,
            'image'             => $image
        ]);

        if($reportCampaign){
            //redirect dengan pesan sukses
            return redirect()->route('admin.campaign.report',$reportCampaign->id)->with(['success' => 'Data Berhasil Disimpan!']);
        }else{
            //redirect dengan pesan error
            return redirect()->route('admin.campaign.report',$reportCampaign->id)->with(['error' => 'Data Gagal Disimpan!']);
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $reportCampaign = ReportCampaign::findOrFail($id);
        return view('admin.report-campaign.edit', compact('reportCampaign'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'title'             => 'required',
            'campaign_id'       => 'required',
            'current_donation'   => 'required|numeric',
            'current_date'          => 'required',
            'description'       => 'required'
        ]); 
        
        $reportCampaign= ReportCampaign::findOrFail($id);
        //check jika image kosong
        if($request->file('image') == '') {
            
            //update data tanpa image
            
            $reportCampaign->update([
                'title'             => $request->title,
                'campaign_id'       => $request->campaign_id,
                'current_donation'  => $request->current_donation,
                'current_date'      => $request->current_date,
                'description'       => $request->description,
            ]);

        } else {

            //hapus image lama
            Storage::disk('local')->delete('public/reportcampaigns/'.basename($reportCampaign->image));

            //upload image baru
            $image = $request->file('image');
            $image->storeAs('public/reportcampaigns', $image->hashName());

            //update dengan image baru
           
            $reportCampaign->update([
                'title'             => $request->title,
                'campaign_id'       => $request->campaign_id,
                'current_donation'  => $request->current_donation,
                'current_date'      => $request->current_date,
                'description'       => $request->description,
                'image'             => $image->hashName()
            ]);
        

      

    }
    if($reportCampaign){
        //redirect dengan pesan sukses
        return redirect()->route('admin.campaign.report',$reportCampaign->campaign_id)->with(['success' => 'Data Berhasil Diupdate!']);
    }else{
        //redirect dengan pesan error
        return redirect()->route('admin.campaign.report',$reportCampaign->campaign_id)->with(['error' => 'Data Gagal Diupdate!']);
    }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $reportCampaign = ReportCampaign::findOrFail($id);
        Storage::disk('local')->delete('public/reportcampaigns/'.basename($reportCampaign->image));
        $reportCampaign->delete();

        if($reportCampaign){
            return response()->json([
                'status' => 'success'
            ]);
        }else{
            return response()->json([
                'status' => 'error'
            ]);
        }
    }
}

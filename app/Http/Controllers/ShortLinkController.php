<?php
  
namespace App\Http\Controllers;
   
use Illuminate\Http\Request;
use App\Models\ShortLink;
use App\Models\LinkData;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
  

class ShortLinkController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $shortLinks = LinkData::orderBy('visits', 'desc')->limit(100)->get();
   
        return view('shortenLink', compact('shortLinks'));
    }
     
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $url = url('/get-link-data');
        $promise = Http::async()->get($url . "?link=".$request->link)->then(function ($response) {
            return;
        });
        /*$request->validate([
           'link' => 'required|url'
        ]);*/
        $find = ShortLink::all();
        $existingCodes = count($find);
        $alphabetCount = count(range('A', 'Z'));
        $nextStringLength = intval(floor($existingCodes/$alphabetCount) + 1);

        $input['link'] = $request->link;
        $input['code'] = $this->getNextRandomCode($nextStringLength);
        
        ShortLink::create($input);
        
        $find = LinkData::firstOrCreate(['link_url' => $input['link']]);
        LinkData::where('link_url', $request->link)->update(['link_code'=> $input['code']]);

        return redirect('generate-shorten-link')
        ->with('success', 'Shorten Link Generated Successfully!');
    }
   
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function shortenLink($code)
    {
        $find = ShortLink::where('code', $code)->first();
        $linkData = LinkData::where('link_code', $code)->first();
        if (strpos($find->lin, "http") !== 0) {
            $url = "http://".$find->link;
        } else {
            $url = $find->link;
        }
        LinkData::where('link_code', $code)->update(['visits'=> $linkData['visits']+1]);
        return redirect()->away($url);
    }

    public function getNextRandomCode($length = 1){
        $alphabet = range('a', 'z');
        $shuffleArrays = [];
        for($i = 0; $i < $length; $i++){
            shuffle($alphabet);
            array_push($shuffleArrays, $alphabet);
        }
        return $this->getNextTestString($shuffleArrays, $length-1, '');
    }

    public function getNextTestString(&$shuffleArrays, $depth, $formedString){
        foreach($shuffleArrays[$depth] as $char){
            $auxFormedString = $formedString . $char;
            if($depth === 0){
                $find = ShortLink::where('code', $auxFormedString)->first();
                if($find) { continue; }
                return $auxFormedString;
            } else {
                $finalFormedString = $this->getNextTestString($shuffleArrays, $depth-1, $auxFormedString);
                if($finalFormedString){
                    return $finalFormedString;
                }
            }
        }
        return false;
    }

    public function getLinkData(Request $request){
        $c = curl_init($request->link);
        curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
        //curl_setopt(... other options you want...)

        $html = curl_exec($c);

        if (curl_error($c))
            return false;

        curl_close($c);
        $title = preg_match('/<title[^>]*>(.*?)<\/title>/ims', $html, $match) ? $match[1] : null;
        $title = $title ?? "No title found";
        $find = LinkData::findOrCreate(['link_url' => $request->link]);
        LinkData::where('link_url', $request->link)->update(['title'=> $title]);
        return $title;
    }
}
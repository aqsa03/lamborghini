<?php

namespace App\Console\Commands;

use App\Models\Live;
use App\Models\PalimpsestItem;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class syncLivePalimpsest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'palimpsestTV:sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get palimpsest xml, parse it and save record into DB';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $live_tv = Live::where('podcast', '=', '0')->firstOr(function () {
            $this->error("Tv Live not found");
            exit;
        });

        $lives = [
            'tv' => [
                'live' => $live_tv,
                'palimpsest_xml_name' => env('PALIMPSEST_XML_TV_NAME')
            ],
            // 'radio' => [
            //     'live' => Live::where('podcast', '=', '1')->first(),
            //     'palimpsest_xml_name' => env('PALIMPSEST_XML_RADIO_NAME')
            // ]
        ];

        foreach($lives as $l){
            if($l['palimpsest_xml_name']){
                if (Storage::disk('palimpsest')->exists($l['palimpsest_xml_name'])) {
                    $xml = Storage::disk('palimpsest')->get($l['palimpsest_xml_name']);
                    $data = new \SimpleXMLElement($xml);
                    $xml_start_date = Carbon::parse($data->TRANSPORT_STREAM->SERVICE->EVENT[0]->attributes()->time)->format('Y-m-d H:i:s');
                    $xml_end_date = Carbon::parse($data->TRANSPORT_STREAM->SERVICE->EVENT[count($data->TRANSPORT_STREAM->SERVICE->EVENT) - 1]->attributes()->time)->add(str_replace('PT', '', $data->TRANSPORT_STREAM->SERVICE->EVENT[count($data->TRANSPORT_STREAM->SERVICE->EVENT) - 1]->attributes()->duration))->format('Y-m-d H:i:s');

                    PalimpsestItem::where([
                        ['start_at', '>=', $xml_start_date],
                        ['start_at', '<=', $xml_end_date]
                    ])->update(['to_delete' => true]);

                    foreach($data->TRANSPORT_STREAM->SERVICE->EVENT as $event){        
                        $program_name = $event->NAME;
                        $description = $event->DESCRIPTION;
                        $start_date = Carbon::parse($event->attributes()->time)->format('Y-m-d H:i:s');
                        $end_date = Carbon::parse($event->attributes()->time)->add(str_replace('PT', '', $event->attributes()->duration))->format('Y-m-d H:i:s');
                        
                        $same_item = PalimpsestItem::where([
                            ['live_id', $l['live']->id],
                            ['start_at', $start_date],
                            ['end_at', $end_date],
                            ['title', $program_name],
                            ['description', $description],
                            ['program_id', Program::where('title', 'like', $program_name)->first()->id ?? null],
                            ['to_delete', true]
                        ])->first();

                        if($same_item){
                            $same_item->to_delete = false;
                            $same_item->saveQuietly();
                        } else {
                            foreach(PalimpsestItem::where('start_at', '<=', $start_date)->where('to_delete', '=', true)->get() as $del){
                                $del->delete();
                            }

                            PalimpsestItem::create([
                                'live_id' => $l['live']->id,
                                'start_at' => $start_date,
                                'end_at' => $end_date,
                                'title' => $program_name,
                                'description' => $description,
                                'program_id' => Program::where('title', 'like', $program_name)->first()->id ?? null
                            ]);
                        }
                    }

                    foreach( PalimpsestItem::where('to_delete', '=', true)->get() as $del){
                        $del->delete();
                    }
                }
            }
        }
    }
}

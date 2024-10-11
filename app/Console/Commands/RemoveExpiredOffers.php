<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Offer;
use Carbon\Carbon;

class RemoveExpiredOffers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'offers:remove-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove or deactivate offers that have expired';

    public function __construct(){
        parent::__construct();
    }
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = Carbon::now();

        $expiredOffers = Offer::where('end_date', '<' ,$now)
                               ->where('active', '=',true )
                               ->get();
        
        foreach($expiredOffers as $offer){
            $offer->active = false;
            $offer->save();
        }
        $this->info('Expired offers removed successfully.');        
    }
}

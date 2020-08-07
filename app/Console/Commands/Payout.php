<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Libraries\TransferWise;
use Str;
use App\Recipient;
use Illuminate\Support\Facades\Validator;

class Payout extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'run:payout {amount?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'System will send money to one recipient account';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $bar = $this->output->createProgressBar(100);
        $bar->start();
        
        $recipient = Recipient::select('recipient_id')->first();
        $is_recipient = (!empty($recipient)) ? 1 : 0;
        if (!$is_recipient) {
            $this->error("\n No recipient found! Please add recipient.");die;
        }
        
        $amount = $this->argument('amount');
        
        $validator = Validator::make(['amount' => $amount], ['amount' => ['numeric']]);
        
        if ($validator->fails()) {
            $this->error("\n Failed to payout. See error messages below:");

            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }
            die;
        }
        
        $amount = $amount ?? 50;
        $this->info("\nThis amount will be transfer: {$amount}");
        
        $bar->advance(5);
        
        $tw_profile = cache('transferwise_profile');
        $profile_id = $tw_profile[0]['id'];
        $this->info("\nProfile ID: {$profile_id}");
        
        $bar->advance(10);
        
        $postdata = [
            'profile' => $profile_id,
            'source' => "GBP",
            'target' => "INR",
            'rateType' => "FIXED",
            'sourceAmount' => $amount,
            'type' => "BALANCE_PAYOUT",
        ];
        
        $tw = new TransferWise;
        $quote = $tw->postRequest('quotes',$postdata);
        
        $bar->advance(10);
        
        $this->info("\nQuote Id: {$quote['id']}");
        $this->info("Source Currency: {$quote['source']}");
        $this->info("Target Currency: {$quote['target']}");
        $this->info("Source Amount: {$quote['sourceAmount']}{$quote['source']}");
        $this->info("Target Amount: {$quote['targetAmount']}{$quote['target']}");
        $this->info("Exchange Rate: {$quote['rate']}");
        $this->info("Exchange Fee: {$quote['fee']}{$quote['source']}");
        
        $quote_id = $quote['id'];
        $transferPurpose = "verification.transfers.purpose.pay.bills";
        $sourceOfFunds = "verification.source.of.funds.other";
        $uuid = (string) Str::uuid();
        
        $posttransfer = [
            'targetAccount' => $recipient->recipient_id,
            'quote' => $quote_id,
            'customerTransactionId' => $uuid,
            'details' => [
                'transferPurpose' => $transferPurpose,
                'sourceOfFunds' => $sourceOfFunds,
            ]
        ];
        
        $transfer = $tw->postRequest('transfers',$posttransfer);
        $transfer_id = $transfer['id'];
        
        $bar->advance(10);
        $this->info("\nStart progress for transfer");
        $this->info("Transfer finished");
        $this->info("Transfer Id: {$transfer_id}");
        
        $postpayout = [
            'type' => "BALANCE"
        ];
        $payout = $tw->payout($profile_id,$transfer_id,$postpayout);
        
        $bar->advance(20);
        $this->info("\nStart progress for payout");
        $this->info("Payout finished");
        $this->info("Payout Type: {$payout['type']}");
        $this->info("Payout Status: {$payout['status']}");
        
        $bar->finish();
    }
}

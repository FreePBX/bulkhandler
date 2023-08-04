#!/usr/bin/php
<?php

/**
 * Converts FreePBX Bulk Extension CSV to CSV recognized by FreePBX Bulk Handler
 *
 * Copyright (c) 2017 Shem Pasamba
 *
 **/

# checks
if (php_sapi_name() != 'cli' AND !defined('STDIN'))
    die("Can only be run in cli.");

if ($_SERVER['argc'] < 2)
    die("syntax: [file.csv]\n");

if (!is_file($_SERVER['argv'][1]))
    die($_SERVER['argv'][1]." is not a file.");
else
    $filename = $_SERVER['argv'][1];

$row = 1;
if (($handle = fopen($filename, "r")) !== FALSE) {
    $headers = fgetcsv($handle, 10240, ",");
    while (($data = fgetcsv($handle, 10240, ",")) !== FALSE)
    {
        $row++;
        [$converted_headers, $converted_data[]] = convert($headers, $data);
    }
    fclose($handle);

    $output_filename = substr((string) $filename, 0, strpos((string) $filename, ".")).'-bulk_handler.csv';
    $fo = fopen($output_filename, "w");
    fputcsv($fo, $converted_headers);
    foreach($converted_data as $row)
        fputcsv($fo, $row);
    fclose($fo);

    echo "Output file is: ".$output_filename."\n";
}

function convert($headers, $data)
{
    $output_data = [];
    $ouput_header = [];

    $column = 0;
    $output_column = 0;
    $ending_voicemail_data = [];
    foreach($headers as $header)
    {
        switch($header)
        {
            case 'action':
            break;
            case 'extension': case 'name':
                $output_data[$output_column] = $data[$column];
                $output_headers[$output_column] = $header;
                $output_column++;
            break;
            case 'cid_masquerade':
            break;
            case 'sipname': case 'outboundcid': case 'ringtimer':
                $output_data[$output_column] = $data[$column];
                $output_headers[$output_column] = $header;
                $output_column++;
            break;
            case 'callwaiting': case 'call_screen': case 'pinless':
            break;
            case 'password': case 'noanswer_dest':
                $output_data[$output_column] = $data[$column];
                $output_headers[$output_column] = $header;
                $output_column++;
            break;
            case 'noanswer_cid':
                $output_data[$output_column] = $data[$column];
                $output_headers[$output_column] = 'noanswer';
                $output_column++;
            break;
            case 'busy_dest': case 'busy_cid': case 'chanunavail_dest':
            case 'chanunavail_cid': case 'emergency_cid': case 'tech':
                $output_data[$output_column] = $data[$column];
                $output_headers[$output_column] = $header;
                $output_column++;
            break;
            case 'hardware': case 'devinfo_channel':
            break;
            case 'devinfo_secret':
                $output_data[$output_column] = $data[$column];
                $output_headers[$output_column] = 'secret';
                $output_column++;
            break;
            case 'devinfo_notransfer':
                $output_data[$output_column] = $data[$column];
                $output_headers[$output_column] = 'dtmfmode';
                $output_column++;
            break;
            case 'devinfo_canreinvite':
                $output_data[$output_column] = $data[$column];
                $output_headers[$output_column] = 'canreinvite';
                $output_column++;
            break;
            case 'devinfo_context':
                $output_data[$output_column] = $data[$column];
                $output_headers[$output_column] = 'context';
                $output_column++;
            break;
            case 'devinfo_immediate': case 'devinfo_signalling':
            case 'devinfo_echocancel': case 'devinfo_echocancelwhenbrdiged':
            case 'devinfo_echotraining': case 'devinfo_busydetect':
            case 'devinfo_busycount': case 'devinfo_callprogress':
            break;
            case 'devinfo_host':
                $output_data[$output_column] = $data[$column];
                $output_headers[$output_column] = 'host';
                $output_column++;
            break;
            case 'devinfo_type':
                $output_data[$output_column] = $data[$column];
                $output_headers[$output_column] = 'type';
                $output_column++;
            break;
            case 'devinfo_nat':
                $output_data[$output_column] = $data[$column];
                $output_headers[$output_column] = 'nat';
                $output_column++;
            break;
            case 'devinfo_port':
                $output_data[$output_column] = $data[$column];
                $output_headers[$output_column] = 'port';
                $output_column++;
            break;
            case 'devinfo_qualify':
                $output_data[$output_column] = $data[$column];
                $output_headers[$output_column] = 'quality';
                $output_column++;
            break;
            case 'devinfo_callgroup':
                $output_data[$output_column] = $data[$column];
                $output_headers[$output_column] = 'namedcallgroup';
                $output_column++;
            break;
            case 'devinfo_pickupgroup':
                $output_data[$output_column] = $data[$column];
                $output_headers[$output_column] = 'namedpickupgroup';
                $output_column++;
            break;
            case 'devinfo_disallow':
                $output_data[$output_column] = $data[$column];
                $output_headers[$output_column] = 'disallow';
                $output_column++;
            break;
            case 'devinfo_allow':
                $output_data[$output_column] = $data[$column];
                $output_headers[$output_column] = 'allow';
                $output_column++;
            break;
            case 'devinfo_dial':
                $output_data[$output_column] = $data[$column];
                $output_headers[$output_column] = 'dial';
                $output_column++;
            break;
            case 'devinfo_accountcode':
                $output_data[$output_column] = $data[$column];
                $output_headers[$output_column] = 'accountcode';
                $output_column++;
            break;
            case 'devinfo_mailbox':
                $output_data[$output_column] = str_replace('@default', '@device', (string) $data[$column]);
                $output_headers[$output_column] = 'mailbox';
                $output_column++;
            break;
            case 'devinfo_deny':
                $output_data[$output_column] = $data[$column];
                $output_headers[$output_column] = 'deny';
                $output_column++;
            break;
            case 'devinfo_permit':
                $output_data[$output_column] = $data[$column];
                $output_headers[$output_column] = 'permit';
                $output_column++;
            break;
            case 'devicetype':
                $output_data[$output_column] = $data[$column];
                $output_headers[$output_column] = $header;
                $output_column++;
            break;
            case 'deviceid':
            break;
            case 'deviceuser':
                $output_data[$output_column] = $data[$column];
                $output_headers[$output_column] = 'id';
                $output_column++;
                $output_data[$output_column] = $data[$column];
                $output_headers[$output_column] = 'user';
                $output_column++;
                $output_data[$output_column] = 'device <'.$data[$column].'>';
                $output_headers[$output_column] = 'callerid';
                $output_column++;
            break;
            case 'description':
                $output_data[$output_column] = $data[$column];
                $output_headers[$output_column] = $header;
                $output_column++;
            break;
            case 'dictenabled': case 'dictformat': case 'dictemail': case 'langcode':
            break;
            case 'record_in':
                $output_data[$output_column] = $data[$column];
                $output_headers[$output_column] = 'recording_in_external';
                $output_column++;
                $output_data[$output_column] = $data[$column];
                $output_headers[$output_column] = 'recording_in_internal';
                $output_column++;
            break;
            case 'record_out':
                $output_data[$output_column] = $data[$column];
                $output_headers[$output_column] = 'recording_out_external';
                $output_column++;
                $output_data[$output_column] = $data[$column];
                $output_headers[$output_column] = 'recording_out_internal';
                $output_column++;
            break;
            case 'vm':
                $output_data[$output_column] = $data[$column]=='enabled'?'yes':'no';
                $output_headers[$output_column] = 'voicemail_enable';
                $output_column++;
            break;
            case 'vmpwd':
                $output_data[$output_column] = $data[$column];
                $output_headers[$output_column] = 'voicemail_vmpwd';
                $output_column++;
            break;
            case 'email':
                $output_data[$output_column] = $data[$column];
                $output_headers[$output_column] = 'voicemail_email';
                $output_column++;
            break;
            case 'pager':
                $output_data[$output_column] = $data[$column];
                $output_headers[$output_column] = 'pager';
                $output_column++;
            break;
            case 'attach': case 'saycid': case 'envelope': case 'delete':
                $ending_voicemail_data[] = $data[$column];
            break;
            case 'options':
            break;
            case 'vmcontext':
                $output_data[$output_column] = $data[$column];
                $output_headers[$output_column] = 'voicemail';
                $output_column++;
                $output_data[$output_column] = $data[$column];
                $output_headers[$output_column] = 'findmefollow_voicemail';
                $output_column++;
            break;
            case 'vmx_state':
            break;
            case 'vmx_unavail_enabled':
                $output_data[$output_column] = $data[$column];
                $output_headers[$output_column] = 'chanunavail_cid';
                $output_column++;
            break;
            case 'vmx_busy_enabled':
                $output_data[$output_column] = $data[$column];
                $output_headers[$output_column] = 'busy_dest';
                $output_column++;
            break;
            case 'vmx_play_instructions': case 'vmx_option_0_sytem_default':
            case 'vmx_option_0_number': case 'vmx_option_1_system_default':
            case 'vmx_option_1_number': case 'vmx_option_2_number':
            case 'account': case 'ddial':
            break;
            case 'pre_ring':
                $output_data[$output_column] = $data[$column];
                $output_headers[$output_column] = 'findmefollow_pre_ring';
                $output_column++;
            break;
            case 'strategy':
                $output_data[$output_column] = $data[$column];
                $output_headers[$output_column] = 'findmefollow_strategy';
                $output_column++;
            break;
            case 'grptime':
                $output_data[$output_column] = $data[$column];
                $output_headers[$output_column] = 'findmefollow_grptime';
                $output_column++;
                $output_data[$output_column] = $data[$column]!=''?'yes':'';
                $output_headers[$output_column] = 'findmefollow_enabled';
                $output_column++;
            break;
            case 'grplist':
                $output_data[$output_column] = $data[$column];
                $output_headers[$output_column] = 'findmefollow_grplist';
                $output_column++;
            break;
            case 'annmsg_id':
                $output_data[$output_column] = $data[$column];
                $output_headers[$output_column] = 'findmefollow_annmsg_id';
                $output_column++;
            break;
            case 'ringing':
                $output_data[$output_column] = $data[$column];
                $output_headers[$output_column] = 'findmefollow_ringing';
                $output_column++;
            break;
            case 'grppre':
                $output_data[$output_column] = $data[$column];
                $output_headers[$output_column] = 'findmefollow_grppre';
                $output_column++;
            break;
            case 'dring':
                $output_data[$output_column] = $data[$column];
                $output_headers[$output_column] = 'findmefollow_dring';
                $output_column++;
            break;
            case 'needsconf':
                $output_data[$output_column] = $data[$column];
                $output_headers[$output_column] = 'findmefollow_needsconf';
                $output_column++;
            break;
            case 'remotealert_id':
                $output_data[$output_column] = $data[$column];
                $output_headers[$output_column] = 'findmefollow_remotealert_id';
                $output_column++;
            break;
            case 'toolate_id':
                $output_data[$output_column] = $data[$column];
                $output_headers[$output_column] = 'findmefollow_toolate_id';
                $output_column++;
            break;
            case 'postdest':
                $output_data[$output_column] = $data[$column];
                $output_headers[$output_column] = 'findmefollow_postdest';
                $output_column++;
            break;
            case 'faxenabled': case 'faxemail':
            break;
            default:
                $output_data[$output_column] = $data[$column];
                $output_headers[$output_column] = $header;
                $output_column++;
            break;
        }
        $column++;
    }
    if (count($ending_voicemail_data) > 0)
    {
        $first_column = true;
        foreach($ending_voicemail_data as $data)
        {
            if ($first_column)
            {
                $first_column = false;
                $output_headers[$output_column] = 'voicemail_options';
            }
            else
                $output_headers[$output_column] = '';
            $output_data[$output_column] = $data;
            $output_column++;
        }
    }
    return [$output_headers, $output_data];
}

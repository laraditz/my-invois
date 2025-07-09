<?php

namespace Laraditz\MyInvois\Enums;

enum Classification: string
{
    case C001 = '001';
    case C002 = '002';
    case C003 = '003';
    case C004 = '004';
    case C005 = '005';
    case C006 = '006';
    case C007 = '007';
    case C008 = '008';
    case C009 = '009';
    case C010 = '010';
    case C011 = '011';
    case C012 = '012';
    case C013 = '013';
    case C014 = '014';
    case C015 = '015';
    case C016 = '016';
    case C017 = '017';
    case C018 = '018';
    case C019 = '019';
    case C020 = '020';
    case C021 = '021';
    case C022 = '022';
    case C023 = '023';
    case C024 = '024';
    case C025 = '025';
    case C026 = '026';
    case C027 = '027';
    case C028 = '028';
    case C029 = '029';
    case C030 = '030';
    case C031 = '031';
    case C032 = '032';
    case C033 = '033';
    case C034 = '034';
    case C035 = '035';
    case C036 = '036';
    case C037 = '037';
    case C038 = '038';
    case C039 = '039';
    case C040 = '040';
    case C041 = '041';
    case C042 = '042';
    case C043 = '043';
    case C044 = '044';
    case C045 = '045';

    public function getDescription(): ?string
    {
        return match ($this) {
            static::C001 => 'Breastfeeding equipment',
            static::C002 => 'Child care centres and kindergartens fees',
            static::C003 => 'Computer, smartphone or tablet',
            static::C004 => 'Consolidated e-Invoice',
            static::C005 => 'Construction materials (as specified under Fourth Schedule of the Lembaga Pembangunan Industri Pembinaan Malaysia Act 1994)',
            static::C006 => 'Disbursement',
            static::C007 => 'Donation',
            static::C008 => 'e-Commerce - e-Invoice to buyer / purchaser',
            static::C009 => 'e-Commerce - Self-billed e-Invoice to seller, logistics, etc.',
            static::C010 => 'Education fees',
            static::C011 => 'Goods on consignment (Consignor)',
            static::C012 => 'Goods on consignment (Consignee)',
            static::C013 => 'Gym membership',
            static::C014 => 'Insurance - Education and medical benefits',
            static::C015 => 'Insurance - Takaful or life insurance',
            static::C016 => 'Interest and financing expenses',
            static::C017 => 'Internet subscription',
            static::C018 => 'Land and building',
            static::C019 => 'Medical examination for learning disabilities and early intervention or rehabilitation treatments of learning disabilities',
            static::C020 => 'Medical examination or vaccination expenses',
            static::C021 => 'Medical expenses for serious diseases',
            static::C022 => 'Others',
            static::C023 => 'Petroleum operations (as defined in Petroleum (Income Tax) Act 1967)',
            static::C024 => 'Private retirement scheme or deferred annuity scheme',
            static::C025 => 'Motor vehicle',
            static::C026 => 'Subscription of books / journals / magazines / newspapers / other similar publications',
            static::C027 => 'Reimbursement',
            static::C028 => 'Rental of motor vehicle',
            static::C029 => 'EV charging facilities (Installation, rental, sale / purchase or subscription fees)',
            static::C030 => 'Repair and maintenance',
            static::C031 => 'Research and development',
            static::C032 => 'Foreign income',
            static::C033 => 'Self-billed - Betting and gaming',
            static::C034 => 'Self-billed - Importation of goods',
            static::C035 => 'Self-billed - Importation of services',
            static::C036 => 'Self-billed - Others',
            static::C037 => 'Self-billed - Monetary payment to agents, dealers or distributors',
            static::C038 => 'Sports equipment, rental / entry fees for sports facilities, registration in sports competition or sports training fees imposed by associations / sports clubs / companies registered with the Sports Commissioner or Companies Commission of Malaysia and carrying out sports activities as listed under the Sports Development Act 1997',
            static::C039 => 'Supporting equipment for disabled person',
            static::C040 => 'Voluntary contribution to approved provident fund',
            static::C041 => 'Dental examination or treatment',
            static::C042 => 'Fertility treatment',
            static::C043 => 'Treatment and home care nursing, daycare centres and residential care centers',
            static::C044 => 'Vouchers, gift cards, loyalty points, etc',
            static::C045 => 'Self-billed - Non-monetary payment to agents, dealers or distributors',
            default => null
        };
    }
}

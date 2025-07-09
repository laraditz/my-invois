<?php

namespace Laraditz\MyInvois\Enums;

enum MSIC: string
{
    // NOT APPLICABLE
    case NotApplicable = '00000';

    // A - AGRICULTURE, FORESTRY AND FISHING
    case A01111 = '01111';
    case A01112 = '01112';
    case A01113 = '01113';
    case A01119 = '01119';
    case A01120 = '01120';
    case A01131 = '01131';
    case A01132 = '01132';
    case A01133 = '01133';
    case A01134 = '01134';
    case A01135 = '01135';
    case A01136 = '01136';
    case A01137 = '01137';
    case A01138 = '01138';
    case A01140 = '01140';
    case A01150 = '01150';
    case A01160 = '01160';
    case A01191 = '01191';
    case A01192 = '01192';
    case A01193 = '01193';
    case A01199 = '01199';
    case A01210 = '01210';
    case A01221 = '01221';
    case A01222 = '01222';
    case A01223 = '01223';
    case A01224 = '01224';
    case A01225 = '01225';
    case A01226 = '01226';
    case A01227 = '01227';
    case A01228 = '01228';
    case A01229 = '01229';
    case A01231 = '01231';
    case A01232 = '01232';
    case A01233 = '01233';
    case A01239 = '01239';
    case A01241 = '01241';
    case A01249 = '01249';
    case A01251 = '01251';
    case A01252 = '01252';
    case A01253 = '01253';
    case A01259 = '01259';
    case A01261 = '01261';
    case A01262 = '01262';
    case A01263 = '01263';
    case A01269 = '01269';
    case A01271 = '01271';
    case A01272 = '01272';
    case A01273 = '01273';
    case A01279 = '01279';
    case A01281 = '01281';
    case A01282 = '01282';
    case A01283 = '01283';
    case A01284 = '01284';
    case A01285 = '01285';
    case A01289 = '01289';
    case A01291 = '01291';
    case A01292 = '01292';
    case A01293 = '01293';
    case A01294 = '01294';
    case A01295 = '01295';
    case A01296 = '01296';
    case A01299 = '01299';
    case A01301 = '01301';
    case A01302 = '01302';
    case A01303 = '01303';
    case A01304 = '01304';
    case A01411 = '01411';
    case A01412 = '01412';

    // B - MINING AND QUARRYING
    case B05000 = '05000';
    case B06000 = '06000';
    case B07000 = '07000';
    case B08000 = '08000';
    case B09000 = '09000';

    // C - MANUFACTURING
    case C10100 = '10100';
    case C10200 = '10200';
    case C10300 = '10300';
    case C10400 = '10400';
    case C10500 = '10500';
    case C10600 = '10600';
    case C10700 = '10700';
    case C10800 = '10800';
    case C10900 = '10900';
    case C11000 = '11000';
    case C12000 = '12000';
    case C13000 = '13000';
    case C14000 = '14000';
    case C15000 = '15000';
    case C16000 = '16000';
    case C17000 = '17000';
    case C18000 = '18000';
    case C19000 = '19000';
    case C20000 = '20000';
    case C21000 = '21000';
    case C22000 = '22000';
    case C23000 = '23000';
    case C24000 = '24000';
    case C25000 = '25000';
    case C26000 = '26000';
    case C27000 = '27000';
    case C28000 = '28000';
    case C29000 = '29000';
    case C30000 = '30000';
    case C31000 = '31000';
    case C32000 = '32000';
    case C33000 = '33000';

    // D - ELECTRICITY, GAS, STEAM AND AIR CONDITIONING SUPPLY
    case D35000 = '35000';
    case D36000 = '36000';
    case D37000 = '37000';
    case D38000 = '38000';
    case D39000 = '39000';

    // E - WATER SUPPLY; SEWERAGE, WASTE MANAGEMENT AND REMEDIATION ACTIVITIES
    case E36000 = '36000';
    case E37000 = '37000';
    case E38000 = '38000';
    case E39000 = '39000';

    // F - CONSTRUCTION
    case F41000 = '41000';
    case F42000 = '42000';
    case F43000 = '43000';

    // G - WHOLESALE AND RETAIL TRADE; REPAIR OF MOTOR VEHICLES AND MOTORCYCLES
    case G45000 = '45000';
    case G46000 = '46000';
    case G47000 = '47000';

    // H - TRANSPORTATION AND STORAGE
    case H49000 = '49000';
    case H50000 = '50000';
    case H51000 = '51000';
    case H52000 = '52000';
    case H53000 = '53000';

    // I - ACCOMMODATION AND FOOD SERVICE ACTIVITIES
    case I55000 = '55000';
    case I56000 = '56000';

    // J - INFORMATION AND COMMUNICATION
    case J58000 = '58000';
    case J59000 = '59000';
    case J60000 = '60000';
    case J61000 = '61000';
    case J62000 = '62000';
    case J63000 = '63000';

    // K - FINANCIAL AND INSURANCE ACTIVITIES
    case K64000 = '64000';
    case K65000 = '65000';
    case K66000 = '66000';

    // L - REAL ESTATE ACTIVITIES
    case L68000 = '68000';

    // M - PROFESSIONAL, SCIENTIFIC AND TECHNICAL ACTIVITIES
    case M69000 = '69000';
    case M70000 = '70000';
    case M71000 = '71000';
    case M72000 = '72000';
    case M73000 = '73000';
    case M74000 = '74000';
    case M75000 = '75000';

    // N - ADMINISTRATIVE AND SUPPORT SERVICE ACTIVITIES
    case N77000 = '77000';
    case N78000 = '78000';
    case N79000 = '79000';
    case N80000 = '80000';
    case N81000 = '81000';
    case N82000 = '82000';

    // O - PUBLIC ADMINISTRATION AND DEFENCE; COMPULSORY SOCIAL SECURITY
    case O84000 = '84000';
    case O85000 = '85000';

    // P - EDUCATION
    case P85000 = '85000';

    // Q - HUMAN HEALTH AND SOCIAL WORK ACTIVITIES
    case Q86000 = '86000';
    case Q87000 = '87000';
    case Q88000 = '88000';

    // R - ARTS, ENTERTAINMENT AND RECREATION
    case R90000 = '90000';
    case R91000 = '91000';
    case R92000 = '92000';
    case R93000 = '93000';

    // S - OTHER SERVICE ACTIVITIES
    case S94000 = '94000';
    case S95000 = '95000';
    case S96000 = '96000';
    case S97000 = '97000';

    // T - ACTIVITIES OF HOUSEHOLDS AS EMPLOYERS; UNDIFFERENTIATED GOODS- AND SERVICES-PRODUCING ACTIVITIES OF HOUSEHOLDS FOR OWN USE
    case T97000 = '97000';
    case T98000 = '98000';
    case T99000 = '99000';

    // U - ACTIVITIES OF EXTRATERRITORIAL ORGANIZATIONS AND BODIES
    case U99000 = '99000';

    public function getDescription(): ?string
    {
        return match ($this) {
            static::NotApplicable => 'NOT APPLICABLE',
            
            // A - AGRICULTURE, FORESTRY AND FISHING
            static::A01111 => 'Growing of maize',
            static::A01112 => 'Growing of leguminous crops',
            static::A01113 => 'Growing of oil seeds',
            static::A01119 => 'Growing of other cereals n.e.c.',
            static::A01120 => 'Growing of paddy',
            static::A01131 => 'Growing of leafy or stem vegetables',
            static::A01132 => 'Growing of fruits bearing vegetables',
            static::A01133 => 'Growing of melons',
            static::A01134 => 'Growing of mushrooms and truffles',
            static::A01135 => 'Growing of vegetables seeds, except beet seeds',
            static::A01136 => 'Growing of other vegetables',
            static::A01137 => 'Growing of sugar beet',
            static::A01138 => 'Growing of roots, tubers, bulb or tuberous vegetables',
            static::A01140 => 'Growing of sugar cane',
            static::A01150 => 'Growing of tobacco',
            static::A01160 => 'Growing of fibre crops',
            static::A01191 => 'Growing of flowers',
            static::A01192 => 'Growing of flower seeds',
            static::A01193 => 'Growing of sago (rumbia)',
            static::A01199 => 'Growing of other non-perennial crops n.e.c.',
            static::A01210 => 'Growing of grapes',
            static::A01221 => 'Growing of banana',
            static::A01222 => 'Growing of mango',
            static::A01223 => 'Growing of durian',
            static::A01224 => 'Growing of rambutan',
            static::A01225 => 'Growing of star fruit',
            static::A01226 => 'Growing of papaya',
            static::A01227 => 'Growing of pineapple',
            static::A01228 => 'Growing of pitaya (dragon fruit)',
            static::A01229 => 'Growing of other tropical and subtropical fruits n.e.c.',
            static::A01231 => 'Growing of pomelo',
            static::A01232 => 'Growing of lemon and limes',
            static::A01233 => 'Growing of tangerines and mandarin',
            static::A01239 => 'Growing of other citrus fruits n.e.c.',
            static::A01241 => 'Growing of guava',
            static::A01249 => 'Growing of other pome fruits and stones fruits n.e.c.',
            static::A01251 => 'Growing of berries',
            static::A01252 => 'Growing of fruit seeds',
            static::A01253 => 'Growing of edible nuts',
            static::A01259 => 'Growing of other tree and bush fruits',
            static::A01261 => 'Growing of oil palm (estate)',
            static::A01262 => 'Growing of oil palm (smallholdings)',
            static::A01263 => 'Growing of coconut (estate and smallholdings)',
            static::A01269 => 'Growing of other oleaginous fruits n.e.c.',
            static::A01271 => 'Growing of coffee',
            static::A01272 => 'Growing of tea',
            static::A01273 => 'Growing of cocoa',
            static::A01279 => 'Growing of other beverage crops n.e.c.',
            static::A01281 => 'Growing of pepper (piper nigrum)',
            static::A01282 => 'Growing of chilies and pepper (capsicum spp.)',
            static::A01283 => 'Growing of nutmeg',
            static::A01284 => 'Growing of ginger',
            static::A01285 => 'Growing of plants used primarily in perfumery, in pharmacy or for insecticidal, fungicidal or similar purposes',
            static::A01289 => 'Growing of other spices and aromatic crops n.e.c.',
            static::A01291 => 'Growing of rubber trees (estate)',
            static::A01292 => 'Growing of rubber trees (smallholdings)',
            static::A01293 => 'Growing of trees for extraction of sap',
            static::A01294 => 'Growing of nipa palm',
            static::A01295 => 'Growing of areca',
            static::A01296 => 'Growing of roselle',
            static::A01299 => 'Growing of other perennial crops n.e.c.',
            static::A01301 => 'Growing of plants for planting',
            static::A01302 => 'Growing of plants for ornamental purposes',
            static::A01303 => 'Growing of live plants for bulbs, tubers and roots; cuttings and slips; mushroom spawn',
            static::A01304 => 'Operation of tree nurseries',
            static::A01411 => 'Raising, breeding and production of cattle or buffaloes',
            static::A01412 => 'Production of raw milk from cattle or buffaloes',
            
            // B - MINING AND QUARRYING
            static::B05000 => 'Mining of coal and lignite',
            static::B06000 => 'Extraction of crude petroleum and natural gas',
            static::B07000 => 'Mining of metal ores',
            static::B08000 => 'Other mining and quarrying',
            static::B09000 => 'Mining support service activities',
            
            // C - MANUFACTURING
            static::C10100 => 'Processing and preserving of meat',
            static::C10200 => 'Processing and preserving of fish, crustaceans and molluscs',
            static::C10300 => 'Processing and preserving of fruit and vegetables',
            static::C10400 => 'Manufacture of vegetable and animal oils and fats',
            static::C10500 => 'Manufacture of dairy products',
            static::C10600 => 'Manufacture of grain mill products, starches and starch products',
            static::C10700 => 'Manufacture of other food products',
            static::C10800 => 'Manufacture of prepared animal feeds',
            static::C10900 => 'Manufacture of beverages',
            static::C11000 => 'Manufacture of tobacco products',
            static::C12000 => 'Manufacture of textiles',
            static::C13000 => 'Manufacture of wearing apparel',
            static::C14000 => 'Manufacture of leather and related products',
            static::C15000 => 'Manufacture of wood and of products of wood and cork, except furniture; manufacture of articles of straw and plaiting materials',
            static::C16000 => 'Manufacture of paper and paper products',
            static::C17000 => 'Printing and reproduction of recorded media',
            static::C18000 => 'Manufacture of coke and refined petroleum products',
            static::C19000 => 'Manufacture of chemicals and chemical products',
            static::C20000 => 'Manufacture of basic pharmaceutical products and pharmaceutical preparations',
            static::C21000 => 'Manufacture of rubber and plastics products',
            static::C22000 => 'Manufacture of other non-metallic mineral products',
            static::C23000 => 'Manufacture of basic metals',
            static::C24000 => 'Manufacture of fabricated metal products, except machinery and equipment',
            static::C25000 => 'Manufacture of computer, electronic and optical products',
            static::C26000 => 'Manufacture of electrical equipment',
            static::C27000 => 'Manufacture of machinery and equipment n.e.c.',
            static::C28000 => 'Manufacture of motor vehicles, trailers and semi-trailers',
            static::C29000 => 'Manufacture of other transport equipment',
            static::C30000 => 'Manufacture of furniture',
            static::C31000 => 'Manufacture of other manufacturing n.e.c.',
            static::C32000 => 'Repair and installation of machinery and equipment',
            static::C33000 => 'Other manufacturing',
            
            // D - ELECTRICITY, GAS, STEAM AND AIR CONDITIONING SUPPLY
            static::D35000 => 'Electric power generation, transmission and distribution',
            static::D36000 => 'Manufacture of gas; distribution of gaseous fuels through mains',
            static::D37000 => 'Steam and air conditioning supply',
            static::D38000 => 'Water collection, treatment and supply',
            static::D39000 => 'Sewerage',
            
            // E - WATER SUPPLY; SEWERAGE, WASTE MANAGEMENT AND REMEDIATION ACTIVITIES
            static::E36000 => 'Manufacture of gas; distribution of gaseous fuels through mains',
            static::E37000 => 'Steam and air conditioning supply',
            static::E38000 => 'Water collection, treatment and supply',
            static::E39000 => 'Sewerage',
            
            // F - CONSTRUCTION
            static::F41000 => 'Construction of buildings',
            static::F42000 => 'Civil engineering',
            static::F43000 => 'Specialized construction activities',
            
            // G - WHOLESALE AND RETAIL TRADE; REPAIR OF MOTOR VEHICLES AND MOTORCYCLES
            static::G45000 => 'Wholesale and retail trade and repair of motor vehicles and motorcycles',
            static::G46000 => 'Wholesale trade, except of motor vehicles and motorcycles',
            static::G47000 => 'Retail trade, except of motor vehicles and motorcycles',
            
            // H - TRANSPORTATION AND STORAGE
            static::H49000 => 'Land transport and transport via pipelines',
            static::H50000 => 'Water transport',
            static::H51000 => 'Air transport',
            static::H52000 => 'Warehousing and support activities for transportation',
            static::H53000 => 'Postal and courier activities',
            
            // I - ACCOMMODATION AND FOOD SERVICE ACTIVITIES
            static::I55000 => 'Accommodation',
            static::I56000 => 'Food and beverage service activities',
            
            // J - INFORMATION AND COMMUNICATION
            static::J58000 => 'Publishing activities',
            static::J59000 => 'Motion picture, video and television programme production, sound recording and music publishing activities',
            static::J60000 => 'Programming and broadcasting activities',
            static::J61000 => 'Telecommunications',
            static::J62000 => 'Computer programming, consultancy and related activities',
            static::J63000 => 'Information service activities',
            
            // K - FINANCIAL AND INSURANCE ACTIVITIES
            static::K64000 => 'Financial service activities, except insurance and pension funding',
            static::K65000 => 'Insurance, reinsurance and pension funding, except compulsory social security',
            static::K66000 => 'Activities auxiliary to financial services and insurance activities',
            
            // L - REAL ESTATE ACTIVITIES
            static::L68000 => 'Real estate activities',
            
            // M - PROFESSIONAL, SCIENTIFIC AND TECHNICAL ACTIVITIES
            static::M69000 => 'Legal and accounting activities',
            static::M70000 => 'Activities of head offices; management consultancy activities',
            static::M71000 => 'Architectural and engineering activities; technical testing and analysis',
            static::M72000 => 'Scientific research and development',
            static::M73000 => 'Advertising and market research',
            static::M74000 => 'Other professional, scientific and technical activities',
            static::M75000 => 'Veterinary activities',
            
            // N - ADMINISTRATIVE AND SUPPORT SERVICE ACTIVITIES
            static::N77000 => 'Rental and leasing activities',
            static::N78000 => 'Employment activities',
            static::N79000 => 'Travel agency, tour operator, reservation service and related activities',
            static::N80000 => 'Security and investigation activities',
            static::N81000 => 'Services to buildings and landscape activities',
            static::N82000 => 'Office administrative, office support and other business support activities',
            
            // O - PUBLIC ADMINISTRATION AND DEFENCE; COMPULSORY SOCIAL SECURITY
            static::O84000 => 'Public administration and defence; compulsory social security',
            static::O85000 => 'Education',
            
            // P - EDUCATION
            static::P85000 => 'Education',
            
            // Q - HUMAN HEALTH AND SOCIAL WORK ACTIVITIES
            static::Q86000 => 'Human health activities',
            static::Q87000 => 'Residential care activities',
            static::Q88000 => 'Social work activities without accommodation',
            
            // R - ARTS, ENTERTAINMENT AND RECREATION
            static::R90000 => 'Creative, arts and entertainment activities',
            static::R91000 => 'Libraries, archives, museums and other cultural activities',
            static::R92000 => 'Gambling and betting activities',
            static::R93000 => 'Sports activities and amusement and recreation activities',
            
            // S - OTHER SERVICE ACTIVITIES
            static::S94000 => 'Activities of membership organizations',
            static::S95000 => 'Repair of computers and personal and household goods',
            static::S96000 => 'Other personal service activities',
            static::S97000 => 'Activities of households as employers of domestic personnel',
            
            // T - ACTIVITIES OF HOUSEHOLDS AS EMPLOYERS; UNDIFFERENTIATED GOODS- AND SERVICES-PRODUCING ACTIVITIES OF HOUSEHOLDS FOR OWN USE
            static::T97000 => 'Activities of households as employers of domestic personnel',
            static::T98000 => 'Undifferentiated goods-producing activities of private households for own use',
            static::T99000 => 'Undifferentiated service-producing activities of private households for own use',
            
            // U - ACTIVITIES OF EXTRATERRITORIAL ORGANIZATIONS AND BODIES
            static::U99000 => 'Activities of extraterritorial organization and bodies',
            
            default => null,
        };
    }

    public function getCategoryReference(): string
    {
        return substr($this->name, 0, 1);
    }

    public function getCategoryName(): string
    {
        return match ($this->getCategoryReference()) {
            'A' => 'AGRICULTURE, FORESTRY AND FISHING',
            'B' => 'MINING AND QUARRYING',
            'C' => 'MANUFACTURING',
            'D' => 'ELECTRICITY, GAS, STEAM AND AIR CONDITIONING SUPPLY',
            'E' => 'WATER SUPPLY; SEWERAGE, WASTE MANAGEMENT AND REMEDIATION ACTIVITIES',
            'F' => 'CONSTRUCTION',
            'G' => 'WHOLESALE AND RETAIL TRADE; REPAIR OF MOTOR VEHICLES AND MOTORCYCLES',
            'H' => 'TRANSPORTATION AND STORAGE',
            'I' => 'ACCOMMODATION AND FOOD SERVICE ACTIVITIES',
            'J' => 'INFORMATION AND COMMUNICATION',
            'K' => 'FINANCIAL AND INSURANCE ACTIVITIES',
            'L' => 'REAL ESTATE ACTIVITIES',
            'M' => 'PROFESSIONAL, SCIENTIFIC AND TECHNICAL ACTIVITIES',
            'N' => 'ADMINISTRATIVE AND SUPPORT SERVICE ACTIVITIES',
            'O' => 'PUBLIC ADMINISTRATION AND DEFENCE; COMPULSORY SOCIAL SECURITY',
            'P' => 'EDUCATION',
            'Q' => 'HUMAN HEALTH AND SOCIAL WORK ACTIVITIES',
            'R' => 'ARTS, ENTERTAINMENT AND RECREATION',
            'S' => 'OTHER SERVICE ACTIVITIES',
            'T' => 'ACTIVITIES OF HOUSEHOLDS AS EMPLOYERS; UNDIFFERENTIATED GOODS- AND SERVICES-PRODUCING ACTIVITIES OF HOUSEHOLDS FOR OWN USE',
            'U' => 'ACTIVITIES OF EXTRATERRITORIAL ORGANIZATIONS AND BODIES',
            default => 'UNKNOWN',
        };
    }

    public static function getByCategory(string $categoryReference): array
    {
        return array_filter(self::cases(), function ($case) use ($categoryReference) {
            return $case->getCategoryReference() === strtoupper($categoryReference);
        });
    }

    public static function getByCode(string $code): ?self
    {
        foreach (self::cases() as $case) {
            if ($case->value === $code) {
                return $case;
            }
        }
        return null;
    }
}

<?php

namespace Database\Seeders;

use App\Models\Property;
use Illuminate\Database\Seeder;

class PropertySeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->properties() as $index => $data) {
            $images = $data['images'];
            unset($data['images']);

            // Keyed on address, not slug, so the model's own slug rule runs.
            $property = Property::updateOrCreate(
                ['address' => $data['address']],
                $data,
            );

            $property->images()->delete();

            $sort = 0;

            foreach ($images as $path => $shot) {
                $property->images()->create([
                    'path'       => $path,
                    'alt'        => $shot.' of '.$data['address'].', '.$data['suburb'],
                    'sort_order' => $sort++,
                ]);
            }
        }
    }

    /**
     * Demo listings. Replace these with real ones through /admin.
     *
     * Photos are CC0 (public domain) stock sourced via Openverse - see
     * public/images/properties/CREDITS.md. They are illustrative only and are
     * not photographs of the addresses named below, which are also invented.
     */
    private function properties(): array
    {
        return [
            [
                'title' => 'Renovated 1 Kanal house on a prime DHA Phase 6 street',
                'status' => 'for_sale', 'type' => 'house',
                'price' => 42500000, 'price_label' => null,
                'address' => 'House 214, Block L, DHA Phase 6', 'suburb' => 'DHA Lahore', 'state' => 'Punjab', 'postcode' => '54792',
                'latitude' => 31.4712, 'longitude' => 74.4359,
                'bedrooms' => 5, 'bathrooms' => 5, 'carspaces' => 3, 'land_size' => 20, 'floor_size' => 4200,
                'is_featured' => true,
                'inspection_times' => 'Saturday and Sunday, 11:00am - 5:00pm, or call for a weekday viewing',
                'features' => ['Clean transfer, all society dues cleared', 'Marble flooring throughout', 'Standby generator and UPS wiring', 'Solar panels (10kW)', 'Basement with home theatre', 'Servant quarter with separate entrance'],
                'description' => 'A 1 Kanal house on one of the quieter interior streets in Block L, renovated end to end about two years ago - not a cosmetic paint job, but new wiring, new plumbing and both kitchens redone.

Five bedrooms with attached baths across two floors, a basement fitted out as a home theatre, and a servant quarter with its own entrance off the back. The lounge opens onto a lawn that gets sun until mid-afternoon.

Transfer is clean and every society due is cleared - I have seen the receipts. Walking distance to Phase 6 park and a five minute drive to LGS.',
                'images' => [
                    'images/properties/p01-1.jpg' => 'Front exterior',
                    'images/properties/p01-2.jpg' => 'Living room',
                    'images/properties/p01-3.jpg' => 'Kitchen',
                    'images/properties/p01-4.jpg' => 'Rear lawn',
                ],
            ],
            [
                'title' => '10 Marla modern house in Bahria Town Sector C',
                'status' => 'for_sale', 'type' => 'house',
                'price' => 21000000, 'price_label' => null,
                'address' => 'House 88, Sector C, Bahria Town', 'suburb' => 'Bahria Town', 'state' => 'Punjab', 'postcode' => '53720',
                'latitude' => 31.3667, 'longitude' => 74.1833,
                'bedrooms' => 4, 'bathrooms' => 4, 'carspaces' => 2, 'land_size' => 10, 'floor_size' => 2600,
                'is_featured' => true,
                'inspection_times' => 'Saturday 12:00pm - 4:00pm',
                'features' => ['Immediate possession', 'Imported kitchen fittings', 'Bahria Town security and backup power', 'Corner plot with extra side lawn', 'Wooden flooring in bedrooms'],
                'description' => 'Built in 2021 and lived in by the original owner, so nothing has been patched over by a flipper. Four bedrooms, all with attached baths, and a drawing room that can shut off from the family lounge.

Bahria Town handles its own security, power backup and maintenance, which is the main reason people pay the premium here - and in a load-shedding summer it earns it.

Grand Mosque and Bahria Head Office are both under ten minutes. Possession is immediate.',
                'images' => [
                    'images/properties/p02-1.jpg' => 'Front exterior',
                    'images/properties/p02-2.jpg' => 'Living room',
                    'images/properties/p02-3.jpg' => 'Kitchen',
                    'images/properties/p02-4.jpg' => 'Side lawn',
                ],
            ],
            [
                'title' => 'Two-bed apartment on Main Boulevard Gulberg',
                'status' => 'for_sale', 'type' => 'apartment',
                'price' => 13500000, 'price_label' => null,
                'address' => 'Apartment 704, Boulevard Heights, Gulberg III', 'suburb' => 'Gulberg', 'state' => 'Punjab', 'postcode' => '54660',
                'latitude' => 31.5204, 'longitude' => 74.3587,
                'bedrooms' => 2, 'bathrooms' => 2, 'carspaces' => 1, 'land_size' => null, 'floor_size' => 1450,
                'inspection_times' => 'Weekdays 4:00pm - 7:00pm by appointment',
                'features' => ['Building generator covers the flat', 'Covered parking', 'Currently rented at PKR 95,000/month', 'Lift and 24-hour security', 'Walk to Liberty Market'],
                'description' => 'Seventh floor, west facing, with a view down Main Boulevard that is genuinely worth having in the evening.

Two bedrooms with attached baths, a proper dining space rather than a token corner, and a balcony deep enough for two chairs and a table. Building has a lift, a standby generator that covers common areas and the flats, and covered parking for one car.

Currently rented at PKR 95,000 a month to a tenant who would happily stay on. Good buy either as an investment or to move into after the lease.',
                'images' => [
                    'images/properties/p04-5.jpg' => 'Building exterior',
                    'images/properties/p04-2.jpg' => 'Main bedroom',
                    'images/properties/p04-1.jpg' => 'Living room',
                    'images/properties/p04-3.jpg' => 'Kitchen',
                    'images/properties/p04-4.jpg' => 'Bathroom',
                ],
            ],
            [
                'title' => '1 Kanal family home on a wide Model Town street',
                'status' => 'for_sale', 'type' => 'house',
                'price' => 38000000, 'price_label' => null,
                'address' => 'House 42, Block J, Model Town', 'suburb' => 'Model Town', 'state' => 'Punjab', 'postcode' => '54700',
                'latitude' => 31.4818, 'longitude' => 74.3162,
                'bedrooms' => 5, 'bathrooms' => 4, 'carspaces' => 2, 'land_size' => 20, 'floor_size' => 3800,
                'inspection_times' => 'Saturday 11:00am - 3:00pm',
                'features' => ['Wide street, low traffic', 'Mature garden with fruit trees', 'High ceilings, solid construction', 'Original kitchen and baths - renovation needed', 'Walk to Model Town Park'],
                'description' => 'An older 1 Kanal house on one of the wide tree-lined streets Model Town is actually known for. Solid construction, high ceilings, and a lawn with a mature mango and two jamun trees.

It has not been renovated, and I am not going to pretend otherwise - the kitchen and baths are original and will want doing. The price reflects that, and the bones are worth the work.

Model Town Park is a two minute walk, and the Link Road gets you to Ferozepur Road in five minutes.',
                'images' => [
                    'images/properties/p04-1.jpg' => 'Front exterior',
                    'images/properties/p04-2.jpg' => 'Living room',
                    'images/properties/p04-3.jpg' => 'Kitchen',
                    'images/properties/p04-4.jpg' => 'Garden',
                ],
            ],
            [
                'title' => '5 Marla house near Emporium in Johar Town',
                'status' => 'under_offer', 'type' => 'house',
                'price' => 16500000, 'price_label' => 'Under offer',
                'address' => 'House 19, Block R1, Johar Town', 'suburb' => 'Johar Town', 'state' => 'Punjab', 'postcode' => '54782',
                'latitude' => 31.4697, 'longitude' => 74.2728,
                'bedrooms' => 3, 'bathrooms' => 3, 'carspaces' => 1, 'land_size' => 5, 'floor_size' => 1800,
                'features' => ['Double storey on 5 Marla', 'Near Emporium Mall and Expo Centre', 'Residential street, not a main road', 'Gas and water connections in order'],
                'description' => 'A tidy 5 Marla double-storey, well suited to a first purchase or a small family. Three bedrooms, a lounge downstairs and a small terrace off the upper landing.

The area around Emporium and Expo Centre has held its value better than most of Johar Town, and the street itself is residential rather than a thoroughfare.

Under offer at present. Call me if you would like to be told first should it fall through.',
                'images' => [
                    'images/properties/p05-1.jpg' => 'Front exterior',
                    'images/properties/p05-2.jpg' => 'Living room',
                    'images/properties/p05-3.jpg' => 'Kitchen',
                    'images/properties/p05-4.jpg' => 'Garden',
                ],
            ],
            [
                'title' => '1 Kanal residential plot, DHA Phase 8 Block S',
                'status' => 'for_sale', 'type' => 'land',
                'price' => 32000000, 'price_label' => null,
                'address' => 'Plot 671, Block S, DHA Phase 8', 'suburb' => 'DHA Lahore', 'state' => 'Punjab', 'postcode' => '54792',
                'latitude' => 31.4869, 'longitude' => 74.4472,
                'bedrooms' => 0, 'bathrooms' => 0, 'carspaces' => 0, 'land_size' => 20, 'floor_size' => null,
                'is_featured' => true,
                'inspection_times' => 'Open plot - visit any time, call for the file',
                'features' => ['Possession available', 'Utilities at the plot', 'Developed pocket, carpeted road', 'Clean file, straightforward transfer'],
                'description' => 'A level 1 Kanal plot in a developed pocket of Block S, so you are building among finished houses rather than on a dust road waiting for neighbours.

Utilities are at the plot, the road is carpeted, and possession is available. The file is clean and I can walk you through the transfer at the DHA office myself.

Plots in this pocket have moved steadily rather than dramatically, which is what you want if you are buying to build rather than to flip.',
                'images' => [
                    'images/properties/p06-1.jpg' => 'Street frontage',
                    'images/properties/p06-2.jpg' => 'Neighbouring build',
                    'images/properties/p06-3.jpg' => 'Nearby house',
                    'images/properties/p06-4.jpg' => 'Aspect',
                ],
            ],
            [
                'title' => '10 Marla house in Askari 11 with army-standard security',
                'status' => 'for_sale', 'type' => 'house',
                'price' => 24500000, 'price_label' => null,
                'address' => 'House 56, Sector B, Askari 11', 'suburb' => 'Askari', 'state' => 'Punjab', 'postcode' => '54000',
                'latitude' => 31.4426, 'longitude' => 74.3611,
                'bedrooms' => 4, 'bathrooms' => 4, 'carspaces' => 2, 'land_size' => 10, 'floor_size' => 2800,
                'inspection_times' => 'Saturday 1:00pm - 5:00pm (entry pass arranged on request)',
                'features' => ['Gated, cantonment-standard security', 'Maintained roads and parks', 'Separate drawing and family lounges', 'Community park at the street end', 'Backup power'],
                'description' => 'Askari 11 buys you something no amount of money buys in most Lahore societies - a gated cantonment-standard perimeter, maintained roads and a management that actually collects and spends the dues.

The house is a well-kept 10 Marla, four bedrooms, with a family lounge separate from the drawing room. Community park is at the end of the street.

Entry requires a pass, so give me a day\'s notice and I will have it arranged before you arrive.',
                'images' => [
                    'images/properties/p07-1.jpg' => 'Front exterior',
                    'images/properties/p07-2.jpg' => 'Living room',
                    'images/properties/p07-3.jpg' => 'Kitchen',
                    'images/properties/p07-4.jpg' => 'Bathroom',
                ],
            ],
            [
                'title' => '2 Kanal colonial bungalow in Lahore Cantt',
                'status' => 'for_sale', 'type' => 'house',
                'price' => null, 'price_label' => 'Price on application',
                'address' => 'House 7, Sarwar Road, Lahore Cantt', 'suburb' => 'Cantt', 'state' => 'Punjab', 'postcode' => '54810',
                'latitude' => 31.5203, 'longitude' => 74.3789,
                'bedrooms' => 6, 'bathrooms' => 5, 'carspaces' => 4, 'land_size' => 40, 'floor_size' => 6200,
                'is_featured' => true,
                'inspection_times' => 'Strictly by appointment',
                'features' => ['Colonial-era construction, high ceilings', 'Deep verandah on two sides', 'Mature 2 Kanal garden', 'Staff accommodation', 'Parking for four cars'],
                'description' => 'A 2 Kanal colonial-era bungalow on Sarwar Road, with the verandah, the ceiling height and the garden that go with it. Six bedrooms, staff accommodation, and off-street parking for four cars.

Houses like this come up perhaps twice a year in Cantt and they are rarely advertised widely. The owners have asked that the price be discussed rather than published.

Serious enquiries only, please - I will need to know who I am bringing before I can arrange a viewing.',
                'images' => [
                    'images/properties/p08-1.jpg' => 'Front exterior',
                    'images/properties/p08-2.jpg' => 'Main bedroom',
                    'images/properties/p08-3.jpg' => 'Kitchen',
                    'images/properties/p08-4.jpg' => 'Garden',
                ],
            ],
            [
                'title' => '1 Kanal house in DHA Phase 5, sold in 21 days',
                'status' => 'sold', 'type' => 'house',
                'price' => 39000000, 'sold_price' => 40500000, 'sold_at' => now()->subDays(16)->toDateString(), 'days_on_market' => 21,
                'address' => 'House 130, Block H, DHA Phase 5', 'suburb' => 'DHA Lahore', 'state' => 'Punjab', 'postcode' => '54792',
                'latitude' => 31.4744, 'longitude' => 74.4028,
                'bedrooms' => 5, 'bathrooms' => 5, 'carspaces' => 2, 'land_size' => 20, 'floor_size' => 4000,
                'features' => ['Sold above asking price', 'Three offers in twelve days', 'Buyer relocating from Islamabad'],
                'description' => 'Listed on a Tuesday, three offers by the second weekend, sold above the asking price to a family relocating from Islamabad.

The owners had been advised by another dealer to spend eighteen lakh on a full renovation before listing. We spent about two lakh on paint, a deep clean and proper photographs instead, and the market did not notice the difference.',
                'images' => [
                    'images/properties/p09-1.jpg' => 'Front exterior',
                    'images/properties/p09-2.jpg' => 'Living room',
                    'images/properties/p09-3.jpg' => 'Kitchen',
                    'images/properties/p09-4.jpg' => 'Rear lawn',
                ],
            ],
            [
                'title' => '10 Marla house in Bahria Town Overseas B',
                'status' => 'sold', 'type' => 'house',
                'price' => 23000000, 'sold_price' => 23500000, 'sold_at' => now()->subDays(38)->toDateString(), 'days_on_market' => 29,
                'address' => 'House 402, Overseas B, Bahria Town', 'suburb' => 'Bahria Town', 'state' => 'Punjab', 'postcode' => '53720',
                'latitude' => 31.3596, 'longitude' => 74.1795,
                'bedrooms' => 4, 'bathrooms' => 4, 'carspaces' => 2, 'land_size' => 10, 'floor_size' => 2700,
                'features' => ['Sold to an overseas buyer', 'Handled via power of attorney', '29 days listing to transfer'],
                'description' => 'Sold to an overseas buyer who never saw the house in person - the whole thing was done on video walkthroughs and a cousin who inspected on their behalf.

Twenty-nine days from listing to transfer, with the paperwork handled through a power of attorney. That process is where most overseas deals fall apart; getting it right in advance is the entire job.',
                'images' => [
                    'images/properties/p10-1.jpg' => 'Front exterior',
                    'images/properties/p10-2.jpg' => 'Living room',
                    'images/properties/p10-3.jpg' => 'Kitchen',
                    'images/properties/p10-4.jpg' => 'Lawn',
                ],
            ],
            [
                'title' => '5 Marla house in Wapda Town, sold to a first-time buyer',
                'status' => 'sold', 'type' => 'house',
                'price' => 14500000, 'sold_price' => 14800000, 'sold_at' => now()->subDays(61)->toDateString(), 'days_on_market' => 26,
                'address' => 'House 88, Block G1, Wapda Town', 'suburb' => 'Wapda Town', 'state' => 'Punjab', 'postcode' => '54770',
                'latitude' => 31.4269, 'longitude' => 74.2506,
                'bedrooms' => 3, 'bathrooms' => 3, 'carspaces' => 1, 'land_size' => 5, 'floor_size' => 1750,
                'features' => ['Street record for a 5 Marla in the block', 'Bank-financed purchase', 'First-time buyers'],
                'description' => 'Bought by a couple purchasing their first home, who had lost out on two earlier houses because their bank financing was not in place before they made an offer.

We got the pre-approval sorted first and then moved quickly. Twenty-six days, and a street record for a 5 Marla in that block by about three lakh.',
                'images' => [
                    'images/properties/p11-1.jpg' => 'Front exterior',
                    'images/properties/p11-2.jpg' => 'Dining room',
                    'images/properties/p11-3.jpg' => 'Kitchen',
                    'images/properties/p11-4.jpg' => 'Garden',
                ],
            ],
            [
                'title' => '1 Kanal plot in DHA Phase 7, sold at file value',
                'status' => 'sold', 'type' => 'land',
                'price' => 27000000, 'sold_price' => 27600000, 'sold_at' => now()->subDays(88)->toDateString(), 'days_on_market' => 34,
                'address' => 'Plot 1204, Block Y, DHA Phase 7', 'suburb' => 'DHA Lahore', 'state' => 'Punjab', 'postcode' => '54792',
                'latitude' => 31.4938, 'longitude' => 74.4614,
                'bedrooms' => 0, 'bathrooms' => 0, 'carspaces' => 0, 'land_size' => 20, 'floor_size' => null,
                'features' => ['Clean file, no disputed dues', 'Transfer completed in one visit', 'Sold to a repeat investor'],
                'description' => 'A clean 1 Kanal file sold to an investor who had been waiting six months for the right plot in Block Y rather than settling for a worse one elsewhere in Phase 7.

No drama, no disputed dues, transfer completed at the DHA office in a single visit. That is what a clean file is worth.',
                'images' => [
                    'images/properties/p12-1.jpg' => 'Street frontage',
                    'images/properties/p12-2.jpg' => 'Main bedroom',
                    'images/properties/p12-3.jpg' => 'Nearby build',
                    'images/properties/p12-4.jpg' => 'Garden',
                ],
            ],
        ];
    }
}

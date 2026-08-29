<?php

namespace Database\Seeders;

use App\Models\Testimonial;
use Illuminate\Database\Seeder;

/**
 * PLACEHOLDER testimonials for the demo. Replace with real ones - these are
 * published under your name and should not stay invented.
 */
class TestimonialSeeder extends Seeder
{
    public function run(): void
    {
        $testimonials = [
            ['DHA Phase 6', 'Seller', 5, true, "Two dealers quoted us prices twenty lakh apart and neither could explain why. Zohaib brought printouts of six recent transfers in our own block and walked us through each one, including the two that argued against the higher figure. We listed where he suggested and sold above it in three weeks.", 'Adnan & Sadia M.'],
            ['Bahria Town', 'Buyer', 5, true, "He told me the society dues on the first house I liked were two years behind and that clearing them was my problem, not the seller's, unless I made it a condition. No other dealer had mentioned it. That one sentence saved me more than his commission.", 'Hassan R.'],
            ['Gulberg', 'Seller', 5, true, "I was in Dubai for the entire sale. Video walkthroughs every week, the power of attorney sorted properly before it was needed, and the transfer done without me flying back once.", 'Farah K.'],
            ['Model Town', 'Seller', 5, false, "What I appreciated was being told not to renovate. Every other dealer wanted us to spend on a new kitchen first. Zohaib said the buyer for this house would want to do their own and we would never see the money back. He was right.", 'Tariq J.'],
            ['DHA Phase 8', 'Buyer', 5, false, "I bought a plot and he checked the file himself at the DHA office before I paid anything. Turned out fine, but the point is he checked rather than telling me it was fine.", 'Usman A.'],
            ['Johar Town', 'Buyer', 4, false, "Answered the phone every time, which sounds like a small thing until you have dealt with dealers who do not. Straightforward about what was wrong with each house as well as what was right.", 'Nida S.'],
            ['Askari 11', 'Seller', 5, false, "Sold in under a month at the price he first quoted, no drama, no last-minute renegotiation. The entry passes for viewings were always arranged before people turned up.", 'Col. (R) Zafar I.'],
            ['Wapda Town', 'Buyer', 5, false, "We had lost two houses because our bank financing was not ready. He made us sort the approval first and then we moved fast on the third. First home, and it went smoothly.", 'Bilal & Ayesha N.'],
            ['Cantt', 'Seller', 5, false, "Discreet, which mattered to us. The house was never put on any portal and he brought exactly four buyers, all of whom were serious.", 'Mrs. Shahnaz Q.'],
        ];

        foreach ($testimonials as $i => [$location, $role, $rating, $featured, $body, $author]) {
            Testimonial::updateOrCreate(
                ['author' => $author],
                [
                    'location'     => $location,
                    'role'         => $role,
                    'rating'       => $rating,
                    'body'         => $body,
                    'is_featured'  => $featured,
                    'is_published' => true,
                    'sort_order'   => $i,
                ],
            );
        }
    }
}

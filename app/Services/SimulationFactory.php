<?php

namespace App\Services;

class SimulationFactory
{
    /**
     * Define the narrative and branched logic for Master Design Simulation.
     */
    public static function getSimulationSet(string $difficulty): array
    {
        switch ($difficulty) {
            case 'beginner':
                return self::beginnerSet();
            case 'intermediate':
                return self::intermediateSet();
            case 'advanced':
                return self::advancedSet();
            case 'expert':
                return self::expertSet();
            case 'master':
                return self::masterSet();
            default:
                return self::beginnerSet();
        }
    }

    private static function beginnerSet(): array
    {
        return [
            'title' => 'The Initial Leap',
            'intro_text' => "Welcome CEO. You just opened your first online store. You have limited budget and must decide quickly where to allocate resources.",
            'difficulty_level' => 'beginner',
            'xp_reward' => 50,
            'steps' => [
                [
                    'question' => 'What pricing strategy do you choose for your first flagship product?',
                    'options' => [
                        [
                            'label' => 'Discount 20%',
                            'effect' => ['profit' => -10, 'traffic' => 30, 'brand' => 5],
                            'feedback' => 'Traffic spikes, but profit margins drop severely. The brand feels slightly cheapened but accessible.'
                        ],
                        [
                            'label' => 'Premium Price',
                            'effect' => ['profit' => 20, 'traffic' => -10, 'brand' => 20],
                            'feedback' => 'Sales are slow, but each sale brings great profit. Your brand image is elevated immediately.'
                        ],
                        [
                            'label' => 'Market Average',
                            'effect' => ['profit' => 5, 'traffic' => 10, 'brand' => 0],
                            'feedback' => 'A safe bet. You get standard traffic and standard profits, blending in with competitors.'
                        ]
                    ]
                ],
                [
                    'question' => 'Which sales channel will you prioritize to launch your product?',
                    'options' => [
                        [
                            'label' => 'TikTok Shop (Viral Focus)',
                            'effect' => ['profit' => -5, 'traffic' => 40, 'brand' => -10],
                            'feedback' => 'Massive exposure! However, platform fees and price wars erode your margin, and your brand looks less exclusive.'
                        ],
                        [
                            'label' => 'Own Website (Brand Focus)',
                            'effect' => ['profit' => 15, 'traffic' => -20, 'brand' => 30],
                            'feedback' => 'Traffic is painfully slow to build, but you control 100% of the data, margins, and the premium brand experience.'
                        ],
                        [
                            'label' => 'Marketplace (Shopee/Tokopedia)',
                            'effect' => ['profit' => 0, 'traffic' => 15, 'brand' => 5],
                            'feedback' => 'Steady organic sales from search traffic, but you are at the mercy of platform algorithms.'
                        ]
                    ]
                ]
            ]
        ];
    }

    private static function intermediateSet(): array
    {
        return [
            'title' => 'Scaling The Funnel',
            'intro_text' => "Your store has survived its first months. Now, you need to pour fuel onto the fire. It is time to design your marketing funnel.",
            'difficulty_level' => 'intermediate',
            'xp_reward' => 100,
            'steps' => [
                [
                    'question' => 'Your Facebook Ads are running. What campaign objective do you choose?',
                    'options' => [
                        [
                            'label' => 'Traffic (Click Link)',
                            'effect' => ['profit' => -15, 'traffic' => 35, 'brand' => 10],
                            'feedback' => 'Lots of cheap clicks! Your website is flooding with visitors, but very few are actually buying (low profit ROI).'
                        ],
                        [
                            'label' => 'Conversion (Purchases)',
                            'effect' => ['profit' => 25, 'traffic' => -5, 'brand' => 5],
                            'feedback' => 'Ads are expensive, but the algorithm finds people ready to buy. Your profit margin looks incredibly healthy.'
                        ],
                        [
                            'label' => 'Brand Awareness',
                            'effect' => ['profit' => -20, 'traffic' => 10, 'brand' => 30],
                            'feedback' => 'Everyone remembers your logo, but your current cashflow suffers a heavy hit.'
                        ]
                    ]
                ],
                [
                    'question' => 'Customers are adding to cart, but 70% abandon it. How do you respond?',
                    'options' => [
                        [
                            'label' => 'Offer 50% Flash Discount',
                            'effect' => ['profit' => -25, 'traffic' => 10, 'brand' => -15],
                            'feedback' => 'They bought it! But you basically paid them to take your product. Your brand positioning looks desperate.'
                        ],
                        [
                            'label' => 'Send WhatsApp Reminder + Free Shipping',
                            'effect' => ['profit' => 10, 'traffic' => 5, 'brand' => 15],
                            'feedback' => 'A gentle, helpful nudge. You swallowed the shipping cost, but saved the healthy product margin and built trust.'
                        ],
                        [
                            'label' => 'Do Nothing, Let Algorithm Work',
                            'effect' => ['profit' => 0, 'traffic' => 0, 'brand' => 0],
                            'feedback' => 'You lost the sales. In the digital world, whoever follows up fastest, wins. You left money on the table.'
                        ]
                    ]
                ]
            ]
        ];
    }

    private static function advancedSet(): array
    {
        return [
            'title' => 'The Cost Optimization War',
            'intro_text' => "Revenue is consistently high, but at the end of the month, your bank account feels empty. It's time to manage the hidden costs.",
            'difficulty_level' => 'advanced',
            'xp_reward' => 150,
            'steps' => [
                [
                    'question' => 'Your server and software subscriptions are eating 20% of your revenue. What is your move?',
                    'options' => [
                        [
                            'label' => 'Migrate to Cheaper Alternatives',
                            'effect' => ['profit' => 30, 'traffic' => -15, 'brand' => -5],
                            'feedback' => 'Costs drop drastically! But the new software is buggy, causing website downtime and frustrating users.'
                        ],
                        [
                            'label' => 'Audit & Cut Unused Tools',
                            'effect' => ['profit' => 20, 'traffic' => 0, 'brand' => 5],
                            'feedback' => 'A smart operational move. You stopped bleeding cash on tools nobody used, improving margins without hurting the experience.'
                        ],
                        [
                            'label' => 'Keep Paying, Focus on Sales',
                            'effect' => ['profit' => -10, 'traffic' => 10, 'brand' => 0],
                            'feedback' => 'Sales continue, but your Burn Rate is dangerously high. You are one bad month away from bankruptcy.'
                        ]
                    ]
                ]
            ]
        ];
    }

    private static function expertSet(): array
    {
        return [
            'title' => 'Competition Annihilation',
            'intro_text' => "A heavily funded competitor just entered your niche. They are copying your ads and undercutting your prices by 40%.",
            'difficulty_level' => 'expert',
            'xp_reward' => 200,
            'steps' => [
                [
                    'question' => 'Competitor drops price by 40%. How do you counter-attack?',
                    'options' => [
                        [
                            'label' => 'Drop prices 50% to kill them',
                            'effect' => ['profit' => -40, 'traffic' => 30, 'brand' => -10],
                            'feedback' => 'A brutal price war. You maintain market share, but you are bleeding capital. Who will run out of money first?'
                        ],
                        [
                            'label' => 'Refuse to drop price, increase service quality',
                            'effect' => ['profit' => 10, 'traffic' => -20, 'brand' => 35],
                            'feedback' => 'You lose budget shoppers, but loyal, high-paying customers stay. You cemented your status as the premium, trusted brand.'
                        ],
                        [
                            'label' => 'Create a cheaper "Lite" sub-brand',
                            'effect' => ['profit' => 15, 'traffic' => 25, 'brand' => 10],
                            'feedback' => 'Brilliant maneuvering. You protect your premium main brand while starving the competitor with your new cheap alternative.'
                        ]
                    ]
                ]
            ]
        ];
    }

    private static function masterSet(): array
    {
        return [
            'title' => 'Crisis & Leadership',
            'intro_text' => "Your main supplier abruptly shuts down. You have 1,000 pending orders and no inventory. The clock is ticking.",
            'difficulty_level' => 'master',
            'xp_reward' => 300,
            'steps' => [
                [
                    'question' => 'Thousands of customers are furious about delayed orders. Action?',
                    'options' => [
                        [
                            'label' => 'Ignore messages while hunting a new supplier',
                            'effect' => ['profit' => 0, 'traffic' => -20, 'brand' => -40],
                            'feedback' => 'Disaster. Silence breeds anger. Your social media is flooded with negative reviews and refund demands.'
                        ],
                        [
                            'label' => 'Full transparent apology + 100% Refund offer',
                            'effect' => ['profit' => -30, 'traffic' => 10, 'brand' => 45],
                            'feedback' => 'It hurts your cashflow severely. But surprisingly, radical transparency earns massive respect. Many customers choose to wait.'
                        ],
                        [
                            'label' => 'Send low-quality substitute products instantly',
                            'effect' => ['profit' => 10, 'traffic' => -10, 'brand' => -35],
                            'feedback' => 'You saved the immediate revenue, but destroyed your brand reputation forever. They will never buy from you again.'
                        ]
                    ]
                ]
            ]
        ];
    }
}

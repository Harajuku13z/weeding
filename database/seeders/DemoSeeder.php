<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Wedding;
use App\Models\WeddingTheme;
use App\Models\WeddingSection;
use App\Models\WeddingProgramItem;
use App\Models\WeddingVenue;
use App\Models\Guest;
use App\Models\GuestResponse;
use App\Models\GiftCategory;
use App\Models\GiftItem;
use App\Models\Rule;
use App\Models\ColorPaletteItem;
use App\Models\MessageTemplate;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        // Admin user
        $admin = User::create([
            'name' => 'Sophie Martin',
            'email' => 'admin@mariage.fr',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // Wedding
        $wedding = Wedding::create([
            'user_id' => $admin->id,
            'bride_name' => 'Sophia',
            'groom_name' => 'Alexandre',
            'slug' => 'sophia-alexandre-2025',
            'quote' => 'L\'amour est notre seul vrai trésor',
            'intro_text' => 'Nous avons l\'immense joie de vous convier à la célébration de notre union.',
            'welcome_message' => 'Bienvenue sur notre page d\'invitation. Nous sommes heureux de partager ce moment magique avec vous.',
            'story_text' => 'Notre histoire a commencé un soir d\'automne à Paris, lors d\'une soirée d\'amis. Un regard, un sourire, et le monde s\'est mis à tourner différemment. Après trois années d\'une belle aventure partagée, il était temps de dire oui pour la vie.',
            'wedding_date' => '2025-09-20',
            'is_published' => true,
            'is_draft' => false,
            'envelope_animation' => true,
            'floral_decor' => true,
            'rsvp_modification_allowed' => true,
            'rsvp_deadline' => '2025-07-15',
            'accommodation_info' => 'Hébergement à proximité',
            'accommodation_details' => "Plusieurs hôtels sont disponibles à proximité du lieu de réception :\n\n🏨 Château de la Forêt (5 min) — Tarif préférentiel avec le code MARIAGE25\n🏨 Hôtel des Arts (10 min) — Navette assurée le soir\n\nNous vous conseillons de réserver rapidement car les disponibilités sont limitées.",
        ]);

        // Theme
        WeddingTheme::create([
            'wedding_id' => $wedding->id,
            'color_primary' => '#c8a97e',
            'color_secondary' => '#f5e6d3',
            'color_accent' => '#8b6355',
            'color_background' => '#fdfaf7',
            'color_text' => '#3d2b1f',
            'font_title' => 'Playfair Display',
            'font_body' => 'Lato',
            'button_style' => 'rounded',
            'border_radius' => '12px',
            'animation_intensity' => 'medium',
            'dress_code_style' => 'Champêtre chic',
            'dress_code_formality' => 'Semi-formel',
            'dress_code_description' => 'Nous vous invitons à porter des tenues légères et élégantes dans des tons naturels, crème, nude, champagne ou terracotta. L\'esprit est champêtre et romantique.',
            'dress_code_men' => 'Costume clair ou lin, cravate ou nœud papillon, chaussures de ville. Évitez le noir strict.',
            'dress_code_women' => 'Robe longue ou mi-longue, tailleur élégant. Talons ou sandales habillées.',
            'dress_code_accessories' => 'Bijoux dorés ou rosés. Chapeau bienvenu pour les femmes.',
            'forbidden_colors' => 'Blanc, ivoire et ecru (réservés à la mariée). Noir strict déconseillé.',
            'mood_description' => 'Une journée baignée de lumière dorée, entre nature et élégance. L\'esprit est romantique, aérien et chaleureux.',
            'mood_textures' => 'Lin, dentelle, soie légère, fleurs séchées, paille tressée',
        ]);

        // Sections par défaut
        $sections = [
            ['key' => 'hero', 'title' => 'Hero', 'sort_order' => 1],
            ['key' => 'story', 'title' => 'Notre histoire', 'sort_order' => 2],
            ['key' => 'gallery', 'title' => 'Galerie', 'sort_order' => 3],
            ['key' => 'details', 'title' => 'Détails', 'sort_order' => 4],
            ['key' => 'program', 'title' => 'Programme', 'sort_order' => 5],
            ['key' => 'venues', 'title' => 'Lieux', 'sort_order' => 6],
            ['key' => 'dresscode', 'title' => 'Dress code', 'sort_order' => 7],
            ['key' => 'gifts', 'title' => 'Cadeaux', 'sort_order' => 9],
            ['key' => 'rules', 'title' => 'Règles', 'sort_order' => 10],
            ['key' => 'rsvp', 'title' => 'RSVP', 'sort_order' => 12],
            ['key' => 'accommodation', 'title' => 'Hébergement', 'sort_order' => 13],
        ];

        foreach ($sections as $section) {
            WeddingSection::create(array_merge($section, ['wedding_id' => $wedding->id]));
        }

        // Programme
        $programItems = [
            ['title' => 'Cérémonie civile', 'date' => '2025-09-20', 'time' => '11:00', 'venue_name' => 'Mairie du 6e arrondissement', 'address' => 'Place Saint-Sulpice, Paris', 'description' => 'Cérémonie officielle en présence des proches.', 'icon' => 'bi-building', 'sort_order' => 1],
            ['title' => 'Cérémonie laïque', 'date' => '2025-09-20', 'time' => '14:00', 'venue_name' => 'Château de Vaux-le-Pénil', 'address' => '77000 Vaux-le-Pénil', 'description' => 'Échange des vœurs dans les jardins du château, sous une pergola fleurie.', 'icon' => 'bi-flower1', 'sort_order' => 2],
            ['title' => 'Vin d\'honneur', 'date' => '2025-09-20', 'time' => '16:00', 'venue_name' => 'Château de Vaux-le-Pénil', 'address' => 'Terrasse du château', 'description' => 'Cocktail champêtre avec tapas, cocktails et musique live.', 'icon' => 'bi-cup-hot-fill', 'sort_order' => 3],
            ['title' => 'Dîner de gala', 'date' => '2025-09-20', 'time' => '20:00', 'venue_name' => 'Grande salle du château', 'address' => 'Château de Vaux-le-Pénil', 'description' => 'Dîner gastronomique, discours, pièce montée et première danse.', 'icon' => 'bi-stars', 'sort_order' => 4],
            ['title' => 'Soirée dansante', 'date' => '2025-09-20', 'time' => '22:30', 'venue_name' => 'Salle de bal', 'address' => null, 'description' => 'La fête continue jusqu\'à l\'aube !', 'icon' => 'bi-music-note-beamed', 'sort_order' => 5],
            ['title' => 'Brunch du lendemain', 'date' => '2025-09-21', 'time' => '11:00', 'venue_name' => 'Jardin du château', 'address' => null, 'description' => 'Brunch convivial pour prolonger la magie de la veille.', 'icon' => 'bi-sun-fill', 'sort_order' => 6],
        ];

        foreach ($programItems as $item) {
            WeddingProgramItem::create(array_merge($item, ['wedding_id' => $wedding->id, 'is_published' => true]));
        }

        // Lieux
        WeddingVenue::create([
            'wedding_id' => $wedding->id,
            'name' => 'Château de Vaux-le-Pénil',
            'address' => '1 avenue de Vaux-le-Pénil',
            'city' => 'Vaux-le-Pénil, 77000',
            'google_maps_url' => 'https://maps.google.com',
            'waze_url' => 'https://waze.com',
            'description' => 'Un château du XVIIe siècle niché dans un parc arboré de 5 hectares, à 40 minutes de Paris.',
            'type' => 'réception',
            'sort_order' => 1,
        ]);

        // Palette couleurs
        $palette = [
            ['name' => 'Champagne', 'hex_color' => '#f5e6d3', 'sort_order' => 1],
            ['name' => 'Terracotta', 'hex_color' => '#c8a97e', 'sort_order' => 2],
            ['name' => 'Bordeaux', 'hex_color' => '#8b6355', 'sort_order' => 3],
            ['name' => 'Olive', 'hex_color' => '#7d8a5e', 'sort_order' => 4],
            ['name' => 'Crème', 'hex_color' => '#fdfaf7', 'sort_order' => 5],
        ];

        foreach ($palette as $color) {
            ColorPaletteItem::create(array_merge($color, ['wedding_id' => $wedding->id]));
        }

        // Cadeaux
        $cat1 = GiftCategory::create(['wedding_id' => $wedding->id, 'name' => 'Notre voyage de noces', 'sort_order' => 1]);
        $cat2 = GiftCategory::create(['wedding_id' => $wedding->id, 'name' => 'La maison', 'sort_order' => 2]);

        GiftItem::create(['wedding_id' => $wedding->id, 'gift_category_id' => $cat1->id, 'name' => 'Contribution au voyage', 'description' => 'Nous partons en voyage de noces à Bali. Toute contribution est la bienvenue !', 'free_contribution' => true, 'sort_order' => 1]);
        GiftItem::create(['wedding_id' => $wedding->id, 'gift_category_id' => $cat1->id, 'name' => 'Nuit au resort', 'price' => 150, 'description' => 'Offrez-nous une nuit dans notre resort de rêve.', 'sort_order' => 2]);
        GiftItem::create(['wedding_id' => $wedding->id, 'gift_category_id' => $cat2->id, 'name' => 'Robot culinaire', 'price' => 299, 'external_link' => 'https://www.amazon.fr', 'sort_order' => 1]);
        GiftItem::create(['wedding_id' => $wedding->id, 'gift_category_id' => $cat2->id, 'name' => 'Set de verres à vin', 'price' => 89, 'is_reserved' => true, 'sort_order' => 2]);

        // Règles
        $rules = [
            ['type' => 'allowed', 'icon' => 'bi-camera-fill', 'title' => 'Photos et vidéos', 'description' => 'N\'hésitez pas à immortaliser ces moments. Partagez vos photos avec le hashtag #SophiaAlexandre2025 !'],
            ['type' => 'allowed', 'icon' => 'bi-emoji-laughing-fill', 'title' => 'Enfants bienvenus', 'description' => 'Les enfants sont les bienvenus. Un espace jeux sera prévu pour eux.'],
            ['type' => 'forbidden', 'icon' => 'bi-camera-video-off-fill', 'title' => 'Pendant la cérémonie', 'description' => 'Merci d\'éteindre vos téléphones pendant les vœux pour vivre pleinement ce moment.'],
            ['type' => 'forbidden', 'icon' => 'bi-clock-fill', 'title' => 'Ponctualité', 'description' => 'Merci d\'arriver 15 minutes avant la cérémonie. Les portes ferment à l\'heure dite.'],
            ['type' => 'recommendation', 'icon' => 'bi-car-front-fill', 'title' => 'Covoiturage', 'description' => 'Nous vous encourageons à organiser des covoiturages. Un groupe WhatsApp sera créé à cet effet.'],
            ['type' => 'recommendation', 'icon' => 'bi-house-heart-fill', 'title' => 'Hébergement', 'description' => 'Pensez à réserver votre hébergement à l\'avance. Nous avons négocié des tarifs préférentiels.'],
        ];

        foreach ($rules as $i => $rule) {
            Rule::create(array_merge($rule, ['wedding_id' => $wedding->id, 'sort_order' => $i + 1, 'is_active' => true]));
        }

        // Invités
        $guestData = [
            ['first_name' => 'Marie', 'last_name' => 'Dupont', 'email' => 'marie.dupont@example.com', 'phone' => '0612345678', 'rsvp_status' => 'accepted', 'companions_allowed' => true, 'max_companions' => 1, 'companions_count' => 1],
            ['first_name' => 'Pierre', 'last_name' => 'Bernard', 'email' => 'pierre.b@example.com', 'phone' => '0698765432', 'rsvp_status' => 'accepted', 'companions_allowed' => true, 'max_companions' => 2, 'companions_count' => 2],
            ['first_name' => 'Lucie', 'last_name' => 'Martin', 'email' => 'lucie.m@example.com', 'rsvp_status' => 'declined'],
            ['first_name' => 'Thomas', 'last_name' => 'Leroy', 'email' => 'thomas.l@example.com', 'rsvp_status' => 'maybe'],
            ['first_name' => 'Emma', 'last_name' => 'Moreau', 'email' => 'emma.m@example.com', 'rsvp_status' => 'pending'],
            ['first_name' => 'Jules', 'last_name' => 'Simon', 'email' => 'jules.s@example.com', 'rsvp_status' => 'pending'],
            ['first_name' => 'Alice', 'last_name' => 'Laurent', 'email' => 'alice.l@example.com', 'rsvp_status' => 'accepted'],
            ['first_name' => 'Hugo', 'last_name' => 'Michel', 'email' => 'hugo.m@example.com', 'rsvp_status' => 'accepted'],
        ];

        foreach ($guestData as $data) {
            $rsvpAt = in_array($data['rsvp_status'], ['accepted', 'declined', 'maybe'])
                ? now()->subDays(rand(1, 20))
                : null;

            Guest::create(array_merge([
                'wedding_id' => $wedding->id,
                'companions_allowed' => false,
                'max_companions' => 0,
                'companions_count' => 0,
                'rsvp_at' => $rsvpAt,
                'contact_channel' => 'email',
            ], $data));
        }

        // Templates de messages
        MessageTemplate::create([
            'wedding_id' => $wedding->id,
            'name' => 'Relance douce',
            'channel' => 'email',
            'subject' => 'Votre réponse pour le mariage de {nom_maries}',
            'body' => "Bonjour {prenom},\n\nNous espérons que vous allez bien !\n\nNous n'avons pas encore reçu votre réponse pour notre mariage le {date_evenement}.\n\nPourriez-vous confirmer votre présence en cliquant sur ce lien ?\n{lien_rsvp}\n\nVotre réponse avant le 15 juillet nous serait très précieuse.\n\nAvec toute notre affection,\n{nom_maries}",
            'type' => 'reminder',
        ]);

        MessageTemplate::create([
            'wedding_id' => $wedding->id,
            'name' => 'Invitation initiale',
            'channel' => 'email',
            'subject' => 'Vous êtes invité(e) au mariage de {nom_maries} 💍',
            'body' => "Bonjour {prenom},\n\nNous avons l'immense joie de vous convier à la célébration de notre mariage le {date_evenement}.\n\nMerci de confirmer votre présence avec votre code personnel : {code_invitation}\n\nOu directement via ce lien :\n{lien_rsvp}\n\nNous serons heureux de partager ce moment inoubliable avec vous.\n\nAvec tout notre amour,\n{nom_maries}",
            'type' => 'invitation',
        ]);

        $this->command->info('✅ Démo créée avec succès !');
        $this->command->info('');
        $this->command->info('📧 Connexion admin : admin@mariage.fr / password');
        $this->command->info('💌 URL publique : /mariage/sophia-alexandre-2025');
        $this->command->info('🎪 Invitée test : Code = ' . Guest::where('wedding_id', $wedding->id)->first()->invitation_code);
    }
}

<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\WeddingController;
use App\Http\Controllers\Admin\GuestController;
use App\Http\Controllers\Admin\ProgramController;
use App\Http\Controllers\Admin\GalleryController;
use App\Http\Controllers\Admin\VenueController;
use App\Http\Controllers\Admin\ThemeController;
use App\Http\Controllers\Admin\GiftController;
use App\Http\Controllers\Admin\RuleController;
use App\Http\Controllers\Admin\ReminderController;
use App\Http\Controllers\Admin\MessageTemplateController;
use App\Http\Controllers\PublicWeddingController;
use App\Http\Controllers\RsvpController;
use App\Http\Controllers\GuestPortalController;
use Illuminate\Http\Middleware\ValidatePostSize;
use Illuminate\Support\Facades\Route;

// ─── Page d'accueil = première invitation publiée ─────────────
Route::get('/', [PublicWeddingController::class, 'home'])->name('home');

// ─── Page publique d'invitation ───────────────────────────────
Route::get('/mariage/{slug}', [PublicWeddingController::class, 'show'])->name('wedding.public');

// ─── Lien personnel invité (sans login) ───────────────────────
// Chaque invité reçoit son lien unique : /i/{code}
Route::get('/i/{code}', [GuestPortalController::class, 'personalPage'])->name('guest.personal');
Route::post('/i/{code}/rsvp', [GuestPortalController::class, 'rsvpSubmit'])->name('guest.rsvp.submit');

// Lien magique par token (envoyé par email)
Route::get('/invitation/{token}', [GuestPortalController::class, 'magicLink'])->name('guest.magic');

// ─── RSVP public (formulaire dans la one-page) ───────────────
Route::get('/rsvp/{wedding}/{code}', [RsvpController::class, 'show'])->name('rsvp.show');
Route::post('/rsvp/{wedding}', [RsvpController::class, 'submit'])->name('rsvp.submit');
Route::get('/rsvp/confirmation/{guest}', [RsvpController::class, 'confirmation'])->name('rsvp.confirmation');

// ─── Routes Admin ───────────────────────────────────────────────
Route::prefix('admin')->name('admin.')->middleware(['auth'])->group(function () {

    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::put('weddings/{wedding}', [WeddingController::class, 'update'])->name('weddings.update')->withoutMiddleware([ValidatePostSize::class]);
    Route::resource('weddings', WeddingController::class)->except(['update']);
    Route::post('weddings/{wedding}/publish', [WeddingController::class, 'publish'])->name('weddings.publish');
    Route::post('weddings/{wedding}/duplicate', [WeddingController::class, 'duplicate'])->name('weddings.duplicate');

    Route::resource('weddings.guests', GuestController::class);
    Route::post('weddings/{wedding}/guests/import', [GuestController::class, 'import'])->name('weddings.guests.import');
    Route::get('weddings/{wedding}/guests/export', [GuestController::class, 'export'])->name('weddings.guests.export');
    Route::post('weddings/{wedding}/guests/{guest}/suspend', [GuestController::class, 'suspend'])->name('weddings.guests.suspend');

    Route::resource('weddings.program', ProgramController::class);
    // Galerie : store sans limite ValidatePostSize pour les gros uploads (php.ini ou ./serve recommandé)
    Route::post('weddings/{wedding}/gallery', [GalleryController::class, 'store'])->name('weddings.gallery.store')->withoutMiddleware([ValidatePostSize::class]);
    Route::resource('weddings.gallery', GalleryController::class)->except(['store']);
    Route::post('weddings/{wedding}/gallery/reorder', [GalleryController::class, 'reorder'])->name('weddings.gallery.reorder');
    Route::resource('weddings.venues', VenueController::class);

    Route::get('weddings/{wedding}/theme', [ThemeController::class, 'edit'])->name('weddings.theme.edit');
    Route::put('weddings/{wedding}/theme', [ThemeController::class, 'update'])->name('weddings.theme.update')->withoutMiddleware([ValidatePostSize::class]);
    Route::delete('weddings/{wedding}/inspirations/{inspirationItem}', [ThemeController::class, 'destroyInspiration'])->name('weddings.inspirations.destroy');

    Route::resource('weddings.gifts', GiftController::class);
    Route::resource('weddings.rules', RuleController::class);

    Route::get('weddings/{wedding}/reminders', [ReminderController::class, 'index'])->name('weddings.reminders.index');
    Route::post('weddings/{wedding}/reminders/send', [ReminderController::class, 'send'])->name('weddings.reminders.send');
    Route::post('weddings/{wedding}/reminders/send-bulk', [ReminderController::class, 'sendBulk'])->name('weddings.reminders.send-bulk');
    Route::get('weddings/{wedding}/reminders/preview', [ReminderController::class, 'preview'])->name('weddings.reminders.preview');

    Route::resource('weddings.templates', MessageTemplateController::class);
});

// ─── Auth Admin ────────────────────────────────────────────────
require __DIR__ . '/auth.php';

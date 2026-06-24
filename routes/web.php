
// Electricity Bill Calculator (SEO Landing Page)
Route::get('/electricity-calculator', function () {
    return response()->file(public_path('electricity-calculator.php'));
})->name('electricity-calculator');

Route::get('/tools/electricity-calculator', function () {
    return response()->file(public_path('electricity-calculator.php'));
});

// TV Size Calculator - Find your optimal TV size
Route::get('tv-size-calculator', function() { return view('tv-size-calculator'); })->name('tv-size-calculator');

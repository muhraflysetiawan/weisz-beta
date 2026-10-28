<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Baris Bahasa Validasi
    |--------------------------------------------------------------------------
    */

    'accepted' => 'Isian :attribute harus diterima.',
    'accepted_if' => 'Isian :attribute harus diterima ketika :other adalah :value.',
    'active_url' => 'Isian :attribute bukan URL yang valid.',
    'after' => 'Isian :attribute harus tanggal setelah :date.',
    'alpha' => 'Isian :attribute hanya boleh berisi huruf.',
    'alpha_dash' => 'Isian :attribute hanya boleh berisi huruf, angka, strip, dan garis bawah.',
    'alpha_num' => 'Isian :attribute hanya boleh berisi huruf dan angka.',
    'array' => 'Isian :attribute harus berupa sebuah array.',
    'before' => 'Isian :attribute harus tanggal sebelum :date.',
    'between' => [
        'numeric' => 'Isian :attribute harus bernilai antara :min dan :max.',
        'file' => 'Isian :attribute harus berukuran antara :min dan :max kilobita.',
        'string' => 'Isian :attribute harus berisi antara :min dan :max karakter.',
        'array' => 'Isian :attribute harus memiliki antara :min dan :max anggota.',
    ],
    'boolean' => 'Isian :attribute harus bernilai benar atau salah.',
    'confirmed' => 'Konfirmasi :attribute tidak cocok.',
    'current_password' => 'Kata sandi saat ini salah.',
    'date' => 'Isian :attribute bukan tanggal yang valid.',
    'email' => 'Isian :attribute harus berupa alamat surel yang valid.',
    'exists' => 'Isian :attribute yang dipilih tidak valid.',
    'max' => [
        'numeric' => 'Isian :attribute maksimal bernilai :max.',
        'file' => 'Isian :attribute maksimal berukuran :max kilobita.',
        'string' => 'Isian :attribute maksimal berisi :max karakter.',
        'array' => 'Isian :attribute maksimal memiliki :max anggota.',
    ],
    'min' => [
        'numeric' => 'Isian :attribute minimal bernilai :min.',
        'file' => 'Isian :attribute minimal berukuran :min kilobita.',
        'string' => 'Isian :attribute minimal berisi :min karakter.',
        'array' => 'Isian :attribute minimal memiliki :min anggota.',
    ],
    'numeric' => 'Isian :attribute harus berupa angka.',
    'password' => [
        'letters' => 'Isian :attribute harus berisi setidaknya satu huruf.',
        'mixed' => 'Isian :attribute harus berisi setidaknya satu huruf besar dan satu huruf kecil.',
        'numbers' => 'Isian :attribute harus berisi setidaknya satu angka.',
        'symbols' => 'Isian :attribute harus berisi setidaknya satu simbol.',
        'uncompromised' => 'Isian :attribute telah muncul dalam kebocoran data. Silakan pilih :attribute yang berbeda.',
    ],
    'required' => 'Kolom :attribute wajib diisi.',
    'required_if' => 'Kolom :attribute wajib diisi bila :other adalah :value.',
    'required_unless' => 'Kolom :attribute wajib diisi kecuali :other memiliki nilai :values.',
    'required_with' => 'Kolom :attribute wajib diisi bila terdapat :values.',
    'required_with_all' => 'Kolom :attribute wajib diisi bila terdapat :values.',
    'required_without' => 'Kolom :attribute wajib diisi bila tidak terdapat :values.',
    'required_without_all' => 'Kolom :attribute wajib diisi bila sama sekali tidak terdapat :values.',
    'same' => 'Isian :attribute dan :other harus sama.',
    'string' => 'Isian :attribute harus berupa string.',
    'unique' => 'Isian :attribute sudah ada sebelumnya.',
    'url' => 'Format isian :attribute tidak valid.',

    'attributes' => [
        'name' => 'nama',
        'email' => 'alamat email',
        'password' => 'kata sandi',
        'current_password' => 'kata sandi saat ini',
    ],

];

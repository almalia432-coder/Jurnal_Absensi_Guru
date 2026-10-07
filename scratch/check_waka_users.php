<?php
foreach (\App\Models\Waka::all() as $w) {
    $g = \App\Models\Guru::where('nip', $w->nip)->first();
    $u = $w->user_id ? \App\Models\User::find($w->user_id) : ($g && $g->user_id ? \App\Models\User::find($g->user_id) : null);
    echo $w->nip . ' | ' . $w->nama_lengkap . ' | waka_uid:' . $w->user_id . ' | guru_uid:' . ($g ? $g->user_id : 'none') . ' | role:' . ($u ? $u->role : 'none') . ' | bidang:' . json_encode($w->bidang_kode) . PHP_EOL;
}

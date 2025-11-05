<div class="min-h-screen bg-gray-50" wire:poll.1000ms>
    <?php
        use Carbon\Carbon;

        if (!function_exists('fmtDate')) {
            function fmtDate($v){ try{ return $v ? Carbon::parse($v)->format('d M Y') : '—'; }catch(\Throwable){ return '—'; } }
        }
        if (!function_exists('fmtTime')) {
            function fmtTime($v){ try{ return $v ? Carbon::parse($v)->format('H:i') : '—'; }catch(\Throwable){ return (is_string($v) && preg_match('/^\d{2}:\d{2}/',$v)) ? substr($v,0,5) : '—'; } }
        }

        // Theme tokens
        $card   = 'bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden';
        $label  = 'block text-sm font-medium text-gray-700 mb-2';
        $input  = 'w-full h-10 px-3 rounded-lg border border-gray-300 text-gray-800 placeholder:text-gray-400 focus:border-gray-900 focus:ring-2 focus:ring-gray-900/10 bg-white transition';
        $btnBlk = 'px-3 py-2 text-xs font-medium rounded-lg bg-gray-900 text-white hover:bg-black focus:outline-none focus:ring-2 focus:ring-gray-900/20 disabled:opacity-60 transition';
        $btnGrn = 'px-3 py-2 text-xs font-medium rounded-lg bg-emerald-600 text-white hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-600/20 disabled:opacity-60 transition';

        $chip      = 'inline-flex items-center gap-2 px-2.5 py-1 rounded-lg bg-gray-100 text-xs';
        $icoAvatar = 'w-10 h-10 bg-gray-900 rounded-xl flex items-center justify-center text-white font-semibold text-sm shrink-0';
        $editIn    = 'w-full h-10 bg-white border border-gray-300 rounded-lg px-3 text-gray-800 focus:border-gray-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-gray-900/10 transition hover:border-gray-400 placeholder:text-gray-400';
    ?>

    <div class="px-4 sm:px-6 py-6 space-y-8">
        
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-gray-900 to-black text-white shadow-2xl">
            <div class="pointer-events-none absolute inset-0 opacity-10">
                <div class="absolute top-0 -right-4 w-24 h-24 bg-white rounded-full blur-xl"></div>
                <div class="absolute bottom-0 -left-4 w-16 h-16 bg-white rounded-full blur-lg"></div>
            </div>
            <div class="relative z-10 p-6 sm:p-8">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-white/10 rounded-xl flex items-center justify-center backdrop-blur-sm border border-white/20">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-lg sm:text-xl font-semibold">Guestbook History</h2>
                            <p class="text-sm text-white/80">Kelola kunjungan aktif dan arsip dengan cepat.</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        <label class="inline-flex items-center gap-2 text-sm text-white/90">
                            <input type="checkbox" wire:model.live="withTrashed"
                                   class="rounded border-white/30 bg-white/10">
                            <span>Include deleted</span>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        
        <section class="<?php echo e($card); ?>">
            <div class="px-6 py-5 bg-gray-50 border-b border-gray-200">
                <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                    <div class="md:col-span-2">
                        <label class="<?php echo e($label); ?>">Search</label>
                        <div class="relative">
                            <input type="text" class="<?php echo e($input); ?> pl-9" placeholder="Cari nama, no HP, instansi, petugas, keperluan…" wire:model.live="q">
                            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m21 21-4.3-4.3M10 18a8 8 0 1 1 0-16 8 8 0 0 1 0 16z" />
                            </svg>
                        </div>
                    </div>

                    <div>
                        <label class="<?php echo e($label); ?>">Tanggal</label>
                        <div class="relative">
                            <input type="date" class="<?php echo e($input); ?> pl-9" wire:model.live="filter_date">
                            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                    </div>

                    <div>
                        <label class="<?php echo e($label); ?>">Urutkan</label>
                        <select class="<?php echo e($input); ?>" wire:model.live="dateMode">
                            <option value="semua">Default (terbaru)</option>
                            <option value="terbaru">Terbaru</option>
                            <option value="terlama">Terlama</option>
                        </select>
                    </div>
                </div>
            </div>
        </section>

        <!-- Two-column layout -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            
            <div class="<?php echo e($card); ?>">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center gap-3">
                        <div class="w-2 h-2 bg-emerald-600 rounded-full"></div>
                        <div>
                            <h3 class="text-base font-semibold text-gray-900">Kunjungan Terbaru (Belum Keluar)</h3>
                            <p class="text-sm text-gray-500">Entri yang masih aktif hari ini</p>
                        </div>
                    </div>
                </div>

                
                <div class="divide-y divide-gray-200">
                    <!--[if BLOCK]><![endif]--><?php $__empty_1 = true; $__currentLoopData = $latest; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php $rowNoLatest = ($latest->firstItem() ?? 1) + $loop->index; ?>
                        <div class="px-6 py-5 hover:bg-gray-50 transition-colors" wire:key="latest-<?php echo e($r->guestbook_id); ?>">
                            <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">
                                
                                <div class="flex items-start gap-3 flex-1 min-w-0">
                                    <div class="<?php echo e($icoAvatar); ?>"><?php echo e(strtoupper(substr($r->name ?? '—', 0, 1))); ?></div>
                                    <div class="min-w-0 flex-1">
                                        <div class="flex items-center gap-2 mb-1.5">
                                            <h4 class="font-semibold text-gray-900 text-base truncate"><?php echo e($r->name); ?></h4>
                                            <!--[if BLOCK]><![endif]--><?php if($r->phone_number): ?>
                                                <span class="text-[11px] text-gray-600 bg-gray-100 px-2 py-0.5 rounded-md"><?php echo e($r->phone_number); ?></span>
                                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                        </div>

                                        <div class="flex flex-wrap gap-1.5 mb-2">
                                            <span class="<?php echo e($chip); ?>">
                                                <svg class="w-3.5 h-3.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                                </svg>
                                                <span class="font-medium text-gray-700"><?php echo e($r->instansi ?? '—'); ?></span>
                                            </span>
                                            <span class="<?php echo e($chip); ?>">
                                                <svg class="w-3.5 h-3.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2" />
                                                </svg>
                                                <span class="font-medium text-gray-700"><?php echo e($r->keperluan ?? '—'); ?></span>
                                            </span>
                                        </div>

                                        <div class="flex flex-wrap items-center gap-4 text-[13px] text-gray-600">
                                            <span class="flex items-center gap-1.5">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                                <?php echo e(fmtDate($r->date)); ?>

                                            </span>
                                            
                                            <span class="flex items-center gap-1.5">
                                                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                          d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                <?php echo e(fmtTime($r->jam_in)); ?>

                                                <span class="mx-1.5">–</span>
                                                <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                          d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                <?php echo e(fmtTime($r->jam_out)); ?>

                                            </span>
                                            <span class="flex items-center gap-1.5">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                                </svg>
                                                <span class="font-medium text-gray-700"><?php echo e($r->petugas_penjaga); ?></span>
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                
                                <div class="text-right shrink-0 space-y-2">
                                    <!--[if BLOCK]><![endif]--><?php if(!empty($r->created_at)): ?>
                                        <div class="text-[11px] text-gray-500"><?php echo e(\Carbon\Carbon::parse($r->created_at)->format('d M Y H:i')); ?></div>
                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                    <div class="flex flex-wrap gap-2 justify-end pt-1.5">
                                        <button
                                            wire:click="openEdit(<?php echo e($r->guestbook_id); ?>)"
                                            wire:loading.attr="disabled"
                                            wire:target="openEdit(<?php echo e($r->guestbook_id); ?>)"
                                            class="<?php echo e($btnBlk); ?>">
                                            <span wire:loading.remove wire:target="openEdit(<?php echo e($r->guestbook_id); ?>)">Edit</span>
                                            <span wire:loading wire:target="openEdit(<?php echo e($r->guestbook_id); ?>)">Memuat…</span>
                                        </button>

                                        <button
                                            wire:click="setJamKeluarNow(<?php echo e($r->guestbook_id); ?>)"
                                            wire:loading.attr="disabled"
                                            wire:target="setJamKeluarNow(<?php echo e($r->guestbook_id); ?>)"
                                            class="<?php echo e($btnGrn); ?>">
                                            <span wire:loading.remove wire:target="setJamKeluarNow(<?php echo e($r->guestbook_id); ?>)">Keluar sekarang</span>
                                            <span wire:loading wire:target="setJamKeluarNow(<?php echo e($r->guestbook_id); ?>)">Menyimpan…</span>
                                        </button>
                                    </div>
                                    <span class="inline-block text-[11px] px-2 py-0.5 rounded-lg bg-gray-100 text-gray-600 border border-gray-200">
                                        No. <?php echo e($rowNoLatest); ?>

                                    </span>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <div class="px-6 py-14 text-center text-gray-500 text-sm">Belum ada kunjungan aktif hari ini</div>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                </div>

                
                <div class="px-6 py-5 bg-gray-50 border-t border-gray-200">
                    <div class="flex justify-center">
                        <?php echo e($latest->onEachSide(1)->links()); ?>

                    </div>
                </div>
            </div>

            
            <div class="<?php echo e($card); ?>">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <div class="w-2 h-2 bg-gray-900 rounded-full"></div>
                            <div>
                                <h3 class="text-base font-semibold text-gray-900">Riwayat Kunjungan</h3>
                                <p class="text-sm text-gray-500">Hanya entri yang sudah keluar</p>
                            </div>
                        </div>
                        <label class="hidden lg:inline-flex items-center gap-2 text-sm text-gray-700">
                            <input type="checkbox" wire:model.live="withTrashed" class="rounded border-gray-300">
                            <span>Include deleted</span>
                        </label>
                    </div>
                </div>

                
                <div class="divide-y divide-gray-200">
                    <!--[if BLOCK]><![endif]--><?php $__empty_1 = true; $__currentLoopData = $entries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php $rowNo = ($entries->firstItem() ?? 1) + $loop->index; ?>
                        <div class="px-6 py-5 hover:bg-gray-50 transition-colors" wire:key="entry-<?php echo e($e->guestbook_id); ?>-<?php echo e($e->deleted_at ? 'trash' : 'ok'); ?>">
                            <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">
                                
                                <div class="flex items-start gap-3 flex-1 min-w-0">
                                    <div class="<?php echo e($icoAvatar); ?>"><?php echo e(strtoupper(substr($e->name ?? '—', 0, 1))); ?></div>
                                    <div class="min-w-0 flex-1">
                                        <div class="flex flex-wrap items-center gap-2 mb-1.5">
                                            <h4 class="font-semibold text-gray-900 text-base truncate"><?php echo e($e->name); ?></h4>
                                            <!--[if BLOCK]><![endif]--><?php if($e->phone_number): ?>
                                                <span class="text-[11px] text-gray-600 bg-gray-100 px-2 py-0.5 rounded-md"><?php echo e($e->phone_number); ?></span>
                                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                            <!--[if BLOCK]><![endif]--><?php if($e->deleted_at): ?>
                                                <span class="text-[11px] px-2 py-0.5 rounded-full bg-red-100 text-red-800">Deleted</span>
                                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                        </div>

                                        <div class="flex flex-wrap gap-1.5 mb-2">
                                            <span class="<?php echo e($chip); ?>">
                                                <svg class="w-3.5 h-3.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                                </svg>
                                                <span class="font-medium text-gray-700"><?php echo e($e->instansi ?? '—'); ?></span>
                                            </span>
                                            <span class="<?php echo e($chip); ?>">
                                                <svg class="w-3.5 h-3.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2" />
                                                </svg>
                                                <span class="font-medium text-gray-700"><?php echo e($e->keperluan ?? '—'); ?></span>
                                            </span>
                                        </div>

                                        <div class="flex flex-wrap items-center gap-4 text-[13px] text-gray-600">
                                            <span class="flex items-center gap-1.5">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                                <?php echo e(fmtDate($e->date)); ?>

                                            </span>
                                            
                                            <span class="flex items-center gap-1.5">
                                                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                          d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                <?php echo e(fmtTime($e->jam_in)); ?>

                                                <span class="mx-1.5">–</span>
                                                <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                          d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                <?php echo e(fmtTime($e->jam_out)); ?>

                                            </span>
                                            <span class="flex items-center gap-1.5">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                                </svg>
                                                <span class="font-medium text-gray-700"><?php echo e($e->petugas_penjaga); ?></span>
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                
                                <div class="text-right shrink-0 space-y-2">
                                    <div class="text-[11px] text-gray-500"><?php echo e(\Carbon\Carbon::parse($e->created_at)->format('d M Y H:i')); ?></div>

                                    <div class="flex flex-wrap gap-2 justify-end pt-1.5">
                                        <button wire:click="openEdit(<?php echo e($e->guestbook_id); ?>)"
                                                wire:loading.attr="disabled"
                                                wire:target="openEdit(<?php echo e($e->guestbook_id); ?>)"
                                                class="<?php echo e($btnBlk); ?>">
                                            <span wire:loading.remove wire:target="openEdit(<?php echo e($e->guestbook_id); ?>)">Edit</span>
                                            <span wire:loading wire:target="openEdit(<?php echo e($e->guestbook_id); ?>)">Memuat…</span>
                                        </button>

                                        <!--[if BLOCK]><![endif]--><?php if(!$e->deleted_at): ?>
                                            
                                            <button wire:click="delete(<?php echo e($e->guestbook_id); ?>)"
                                                    onclick="return confirm('Hapus entri ini?')"
                                                    wire:loading.attr="disabled"
                                                    wire:target="delete(<?php echo e($e->guestbook_id); ?>)"
                                                    class="px-3 py-2 text-xs font-medium rounded-lg bg-rose-600 text-white hover:bg-rose-700 focus:outline-none focus:ring-2 focus:ring-rose-600/20 disabled:opacity-60 transition">
                                                <span wire:loading.remove wire:target="delete(<?php echo e($e->guestbook_id); ?>)">Hapus</span>
                                                <span wire:loading wire:target="delete(<?php echo e($e->guestbook_id); ?>)">Menghapus…</span>
                                            </button>
                                        <?php else: ?>
                                            
                                            <button wire:click="restore(<?php echo e($e->guestbook_id); ?>)"
                                                    wire:loading.attr="disabled"
                                                    wire:target="restore(<?php echo e($e->guestbook_id); ?>)"
                                                    class="px-3 py-2 text-xs font-medium rounded-lg bg-emerald-600 text-white hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-600/20 disabled:opacity-60 transition">
                                                <span wire:loading.remove wire:target="restore(<?php echo e($e->guestbook_id); ?>)">Restore</span>
                                                <span wire:loading wire:target="restore(<?php echo e($e->guestbook_id); ?>)">Memproses…</span>
                                            </button>

                                            
                                            <button wire:click="destroyForever(<?php echo e($e->guestbook_id); ?>)"
                                                    onclick="return confirm('Hapus permanen entri ini? Tindakan tidak bisa dibatalkan!')"
                                                    wire:loading.attr="disabled"
                                                    wire:target="destroyForever(<?php echo e($e->guestbook_id); ?>)"
                                                    class="px-3 py-2 text-xs font-medium rounded-lg bg-rose-700 text-white hover:bg-rose-800 focus:outline-none focus:ring-2 focus:ring-rose-700/20 disabled:opacity-60 transition">
                                                <span wire:loading.remove wire:target="destroyForever(<?php echo e($e->guestbook_id); ?>)">Hapus Permanen</span>
                                                <span wire:loading wire:target="destroyForever(<?php echo e($e->guestbook_id); ?>)">Menghapus…</span>
                                            </button>
                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                    </div>

                                    <span class="inline-block text-[11px] px-2 py-0.5 rounded-lg bg-gray-100 text-gray-600 border border-gray-200">
                                        No. <?php echo e($rowNo); ?>

                                    </span>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <div class="px-6 py-14 text-center text-gray-500 text-sm">Tidak ada entri kunjungan yang ditemukan</div>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                </div>

                
                <div class="px-6 py-5 bg-gray-50 border-t border-gray-200">
                    <div class="flex justify-center">
                        <?php echo e($entries->onEachSide(1)->links()); ?>

                    </div>
                </div>
            </div>

        </div>
        <!-- /Two-column layout -->
    </div>

    
    <!--[if BLOCK]><![endif]--><?php if($showEdit): ?>
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-black/60 backdrop-blur-sm transition-all duration-300" wire:click="closeEdit"></div>
            <div class="relative w-full max-w-lg bg-white rounded-xl shadow-2xl border border-gray-200 overflow-hidden transform transition-all duration-300 scale-100 max-h-[90vh] flex flex-col">
                <div class="bg-gradient-to-r from-gray-900 to-black p-5 text-white relative overflow-hidden">
                    <div class="absolute inset-0 opacity-10 pointer-events-none">
                        <div class="absolute top-0 -right-6 w-24 h-24 bg-white rounded-full blur-2xl"></div>
                        <div class="absolute bottom-0 -left-4 w-16 h-16 bg-white rounded-full blur-xl"></div>
                    </div>
                    <div class="relative z-10 flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-semibold tracking-tight">Edit Entri</h3>
                            <div class="flex items-center gap-2 mt-1">
                                <div class="w-2 h-2 bg-emerald-400 rounded-full animate-pulse"></div>
                                <p class="text-[11px] text-gray-200 font-mono"><?php echo e($this->serverClock); ?></p>
                            </div>
                        </div>
                        <button class="w-9 h-9 rounded-full bg-white/10 hover:bg-white/20 backdrop-blur-sm border border-white/20 flex items-center justify-center transition-all duration-200" wire:click="closeEdit">
                            <svg class="w-4.5 h-4.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="p-5 space-y-4 overflow-y-auto flex-1">
                    <div class="space-y-1.5">
                        <label class="<?php echo e($label); ?>">Tanggal</label>
                        <div class="relative">
                            <input type="date" wire:model="edit.date" class="<?php echo e($editIn); ?>">
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <svg class="w-4.5 h-4.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                        </div>
                        <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['edit.date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-[11px] text-red-600 font-medium flex items-center gap-1">
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                            <?php echo e($message); ?>

                        </p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                    </div>

                    <div class="grid grid-cols-2 gap-3.5">
                        <div class="space-y-1.5">
                            <label class="<?php echo e($label); ?>">Jam Masuk</label>
                            <input type="time" wire:model="edit.jam_in" class="<?php echo e($editIn); ?>">
                            <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['edit.jam_in'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-[11px] text-red-600 font-medium"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                        </div>
                        <div class="space-y-1.5">
                            <label class="<?php echo e($label); ?>">Jam Keluar</label>
                            <input type="time" wire:model="edit.jam_out" class="<?php echo e($editIn); ?>">
                            <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['edit.jam_out'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-[11px] text-red-600 font-medium"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                            <div class="bg-gray-50 rounded-md p-3 border border-gray-200">
                                <p class="text-[11px] text-gray-600 leading-relaxed">
                                    💡 <span class="font-medium">Tips:</span> Klik <span class="font-semibold text-gray-900">Keluar sekarang</span> di "Kunjungan Terbaru" untuk menggunakan waktu real-time.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3.5">
                        <div class="space-y-1.5">
                            <label class="<?php echo e($label); ?>">Nama</label>
                            <input type="text" wire:model="edit.name" placeholder="Masukkan nama lengkap" class="<?php echo e($editIn); ?>">
                            <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['edit.name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-[11px] text-red-600 font-medium"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                        </div>
                        <div class="space-y-1.5">
                            <label class="<?php echo e($label); ?>">No HP</label>
                            <input type="text" wire:model="edit.phone_number" placeholder="08xxxxxxxxxx" class="<?php echo e($editIn); ?>">
                        </div>
                    </div>

                    <div class="space-y-3.5">
                        <div class="space-y-1.5">
                            <label class="<?php echo e($label); ?>">Instansi</label>
                            <input type="text" wire:model="edit.instansi" placeholder="Nama perusahaan/instansi" class="<?php echo e($editIn); ?>">
                        </div>
                        <div class="space-y-1.5">
                            <label class="<?php echo e($label); ?>">Keperluan</label>
                            <input type="text" wire:model="edit.keperluan" placeholder="Tujuan kunjungan" class="<?php echo e($editIn); ?>">
                        </div>
                        <div class="space-y-1.5">
                            <label class="<?php echo e($label); ?>">Petugas Penjaga</label>
                            <input type="text" wire:model="edit.petugas_penjaga" placeholder="Nama petugas yang bertugas" class="<?php echo e($editIn); ?>">
                            <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['edit.petugas_penjaga'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-[11px] text-red-600 font-medium"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 border-t border-gray-200 p-5">
                    <div class="flex items-center justify-end gap-2.5">
                        <button type="button" wire:click="closeEdit" class="px-4 h-10 rounded-lg border border-gray-300 text-gray-700 text-sm font-medium hover:bg-gray-100 hover:border-gray-400 transition">Batal</button>
                        <button type="button" wire:click="saveEdit" wire:loading.attr="disabled" wire:target="saveEdit" class="px-4 h-10 rounded-lg bg-gray-900 text-white text-sm font-medium hover:bg-black focus:outline-none focus:ring-2 focus:ring-gray-900/20 disabled:opacity-60 transition shadow-sm">
                            <span wire:loading.remove wire:target="saveEdit" class="flex items-center gap-1.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Simpan Perubahan
                            </span>
                            <span wire:loading wire:target="saveEdit" class="flex items-center gap-2">
                                <svg class="animate-spin -ml-1 mr-1 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/>
                                </svg>
                                Menyimpan…
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
</div>
<?php /**PATH /home/haelahpx/Documents/GitHub/Frontend-KRB/resources/views/livewire/pages/receptionist/guestbookhistory.blade.php ENDPATH**/ ?>
{{--
  Bulk action toolbar — include inside a div with x-data="bulkSelect('module_name')"
  Required: $module (string)
  Optional: $bulkCategories, $bulkTags, $bulkParents (collections for assign actions)
--}}
<div x-show="selected.length > 0"
     x-transition:enter="transition ease-out duration-200"
     x-transition:enter-start="opacity-0 -translate-y-1"
     x-transition:enter-end="opacity-100 translate-y-0"
     x-transition:leave="transition ease-in duration-150"
     x-transition:leave-start="opacity-100 translate-y-0"
     x-transition:leave-end="opacity-0 -translate-y-1"
     class="mb-4 flex flex-wrap items-center gap-2 bg-white border border-primary/25 shadow-sm rounded-2xl px-5 py-3"
     style="display:none">

    <span class="font-outfit text-sm text-primary font-semibold shrink-0"
          x-text="selected.length + (selected.length === 1 ? ' element seleccionat' : ' elements seleccionats')"></span>

    <div class="h-4 w-px bg-gray-200 shrink-0"></div>

    {{-- ── Users ─────────────────────────────────────────────────────── --}}
    @if($module === 'users')
        <button type="button" @click="submitBulk('approve')"
                class="font-outfit text-xs px-3 py-1.5 rounded-xl bg-green-50 text-green-700 hover:bg-green-100 transition-colors">
            Aprovar
        </button>
        <button type="button" @click="submitBulk('reject')"
                class="font-outfit text-xs px-3 py-1.5 rounded-xl bg-orange-50 text-orange-600 hover:bg-orange-100 transition-colors">
            Rebutjar
        </button>
        <button type="button"
                @click="if(confirm('Eliminar els usuaris seleccionats?')) submitBulk('delete')"
                class="font-outfit text-xs px-3 py-1.5 rounded-xl bg-red-600 text-white hover:bg-red-700 transition-colors">
            Eliminar
        </button>

    {{-- ── Categories ────────────────────────────────────────────────── --}}
    @elseif($module === 'categories')
        <button type="button" @click="submitBulk('activate')"
                class="font-outfit text-xs px-3 py-1.5 rounded-xl bg-green-50 text-green-700 hover:bg-green-100 transition-colors">
            Activar
        </button>
        <button type="button" @click="submitBulk('deactivate')"
                class="font-outfit text-xs px-3 py-1.5 rounded-xl bg-gray-100 text-gray-600 hover:bg-gray-200 transition-colors">
            Desactivar
        </button>
        @if(!empty($bulkParents) && count($bulkParents))
            <div class="h-4 w-px bg-gray-200 shrink-0"></div>
            <div class="flex items-center gap-1.5">
                <select x-model="assignParentId"
                        class="border border-gray-200 rounded-xl px-3 py-1.5 font-outfit text-xs focus:outline-none focus:ring-2 focus:ring-primary/40">
                    <option value="">Sense pare (arrel)</option>
                    @foreach($bulkParents as $p)
                        <option value="{{ $p->id }}">{{ $p->getTranslation('name', 'ca') }}</option>
                    @endforeach
                </select>
                <button type="button" @click="submitBulk('assign_parent', {parent_id: assignParentId})"
                        class="font-outfit text-xs px-3 py-1.5 rounded-xl bg-primary text-white hover:brightness-110 transition-all">
                    Assignar pare
                </button>
            </div>
        @endif
        <div class="h-4 w-px bg-gray-200 shrink-0"></div>
        <button type="button"
                @click="if(confirm('Eliminar les categories seleccionades?')) submitBulk('delete')"
                class="font-outfit text-xs px-3 py-1.5 rounded-xl bg-red-600 text-white hover:bg-red-700 transition-colors">
            Eliminar
        </button>

    {{-- ── Products ──────────────────────────────────────────────────── --}}
    @elseif($module === 'products')
        <button type="button" @click="submitBulk('activate')"
                class="font-outfit text-xs px-3 py-1.5 rounded-xl bg-green-50 text-green-700 hover:bg-green-100 transition-colors">
            Activar
        </button>
        <button type="button" @click="submitBulk('deactivate')"
                class="font-outfit text-xs px-3 py-1.5 rounded-xl bg-gray-100 text-gray-600 hover:bg-gray-200 transition-colors">
            Desactivar
        </button>
        <button type="button" @click="submitBulk('featured')"
                class="font-outfit text-xs px-3 py-1.5 rounded-xl bg-yellow-50 text-yellow-700 hover:bg-yellow-100 transition-colors">
            Destacar
        </button>
        <button type="button" @click="submitBulk('unfeatured')"
                class="font-outfit text-xs px-3 py-1.5 rounded-xl bg-gray-100 text-gray-500 hover:bg-gray-200 transition-colors">
            No destacar
        </button>
        @if(!empty($bulkCategories) && count($bulkCategories))
            <div class="h-4 w-px bg-gray-200 shrink-0"></div>
            <div class="flex items-center gap-1.5">
                <select x-model="assignCategoryId"
                        class="border border-gray-200 rounded-xl px-3 py-1.5 font-outfit text-xs focus:outline-none focus:ring-2 focus:ring-primary/40">
                    <option value="">Sel·lecciona categoria…</option>
                    @foreach($bulkCategories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->getTranslation('name', 'ca') }}</option>
                    @endforeach
                </select>
                <button type="button" @click="submitBulk('assign_category', {category_id: assignCategoryId})"
                        class="font-outfit text-xs px-3 py-1.5 rounded-xl bg-primary text-white hover:brightness-110 transition-all">
                    Assignar cat.
                </button>
            </div>
        @endif
        @if(!empty($bulkTags) && count($bulkTags))
            <div class="flex items-center gap-1.5">
                <select x-model="assignTagIds" multiple
                        class="border border-gray-200 rounded-xl px-3 py-1.5 font-outfit text-xs focus:outline-none focus:ring-2 focus:ring-primary/40 max-h-8">
                    @foreach($bulkTags as $tag)
                        <option value="{{ $tag->id }}">{{ $tag->name }}</option>
                    @endforeach
                </select>
                <button type="button" @click="submitBulk('assign_tags', {tags: assignTagIds})"
                        class="font-outfit text-xs px-3 py-1.5 rounded-xl bg-secondary text-white hover:brightness-110 transition-all">
                    Assignar etiq.
                </button>
            </div>
        @endif
        <div class="h-4 w-px bg-gray-200 shrink-0"></div>
        <button type="button"
                @click="if(confirm('Eliminar els productes seleccionats?')) submitBulk('delete')"
                class="font-outfit text-xs px-3 py-1.5 rounded-xl bg-red-600 text-white hover:bg-red-700 transition-colors">
            Eliminar
        </button>

    {{-- ── Orders ────────────────────────────────────────────────────── --}}
    @elseif($module === 'orders')
        <div class="flex items-center gap-1.5">
            <select x-model="bulkStatus"
                    class="border border-gray-200 rounded-xl px-3 py-1.5 font-outfit text-xs focus:outline-none focus:ring-2 focus:ring-primary/40">
                <option value="">Canviar estat a…</option>
                <option value="processing">En procés</option>
                <option value="shipped">Enviat</option>
                <option value="delivered">Lliurat</option>
                <option value="cancelled">Cancel·lat</option>
            </select>
            <button type="button" @click="if(bulkStatus) submitBulk('set_status', {status: bulkStatus})"
                    :disabled="!bulkStatus"
                    class="font-outfit text-xs px-3 py-1.5 rounded-xl bg-primary text-white hover:brightness-110 transition-all disabled:opacity-40 disabled:cursor-not-allowed">
                Aplicar
            </button>
        </div>

    {{-- ── Quotations ────────────────────────────────────────────────── --}}
    @elseif($module === 'quotations')
        <div class="flex items-center gap-1.5">
            <select x-model="bulkStatus"
                    class="border border-gray-200 rounded-xl px-3 py-1.5 font-outfit text-xs focus:outline-none focus:ring-2 focus:ring-primary/40">
                <option value="">Canviar estat a…</option>
                <option value="reviewing">En revisió</option>
                <option value="quoted">Pressupostat</option>
                <option value="accepted">Acceptat</option>
                <option value="rejected">Rebutjat</option>
            </select>
            <button type="button" @click="if(bulkStatus) submitBulk('set_status', {status: bulkStatus})"
                    :disabled="!bulkStatus"
                    class="font-outfit text-xs px-3 py-1.5 rounded-xl bg-primary text-white hover:brightness-110 transition-all disabled:opacity-40 disabled:cursor-not-allowed">
                Aplicar
            </button>
        </div>
        <div class="h-4 w-px bg-gray-200 shrink-0"></div>
        <button type="button"
                @click="if(confirm('Eliminar els pressupostos seleccionats?')) submitBulk('delete')"
                class="font-outfit text-xs px-3 py-1.5 rounded-xl bg-red-600 text-white hover:bg-red-700 transition-colors">
            Eliminar
        </button>

    {{-- ── Quote Requests ────────────────────────────────────────────── --}}
    @elseif($module === 'quote_requests')
        <button type="button" @click="submitBulk('mark_in_review')"
                class="font-outfit text-xs px-3 py-1.5 rounded-xl bg-blue-50 text-blue-700 hover:bg-blue-100 transition-colors">
            Marcar en revisió
        </button>
        <button type="button" @click="submitBulk('close')"
                class="font-outfit text-xs px-3 py-1.5 rounded-xl bg-gray-100 text-gray-600 hover:bg-gray-200 transition-colors">
            Tancar
        </button>
        <div class="h-4 w-px bg-gray-200 shrink-0"></div>
        <button type="button"
                @click="if(confirm('Eliminar les sol·licituds seleccionades?')) submitBulk('delete')"
                class="font-outfit text-xs px-3 py-1.5 rounded-xl bg-red-600 text-white hover:bg-red-700 transition-colors">
            Eliminar
        </button>

    {{-- ── Contact Messages ──────────────────────────────────────────── --}}
    @elseif($module === 'contact_messages')
        <button type="button" @click="submitBulk('mark_read')"
                class="font-outfit text-xs px-3 py-1.5 rounded-xl bg-green-50 text-green-700 hover:bg-green-100 transition-colors">
            Marcar llegit
        </button>
        <button type="button" @click="submitBulk('mark_unread')"
                class="font-outfit text-xs px-3 py-1.5 rounded-xl bg-gray-100 text-gray-600 hover:bg-gray-200 transition-colors">
            Marcar no llegit
        </button>
        <div class="h-4 w-px bg-gray-200 shrink-0"></div>
        <button type="button"
                @click="if(confirm('Eliminar els missatges seleccionats?')) submitBulk('delete')"
                class="font-outfit text-xs px-3 py-1.5 rounded-xl bg-red-600 text-white hover:bg-red-700 transition-colors">
            Eliminar
        </button>

    {{-- ── Companies ─────────────────────────────────────────────────── --}}
    @elseif($module === 'companies')
        <button type="button" @click="submitBulk('activate')"
                class="font-outfit text-xs px-3 py-1.5 rounded-xl bg-green-50 text-green-700 hover:bg-green-100 transition-colors">
            Activar
        </button>
        <button type="button" @click="submitBulk('deactivate')"
                class="font-outfit text-xs px-3 py-1.5 rounded-xl bg-gray-100 text-gray-600 hover:bg-gray-200 transition-colors">
            Desactivar
        </button>
        <div class="h-4 w-px bg-gray-200 shrink-0"></div>
        <button type="button"
                @click="if(confirm('Eliminar les empreses seleccionades?')) submitBulk('delete')"
                class="font-outfit text-xs px-3 py-1.5 rounded-xl bg-red-600 text-white hover:bg-red-700 transition-colors">
            Eliminar
        </button>

    {{-- ── Print Templates ───────────────────────────────────────────── --}}
    @elseif($module === 'print_templates')
        <button type="button" @click="submitBulk('activate')"
                class="font-outfit text-xs px-3 py-1.5 rounded-xl bg-green-50 text-green-700 hover:bg-green-100 transition-colors">
            Activar
        </button>
        <button type="button" @click="submitBulk('deactivate')"
                class="font-outfit text-xs px-3 py-1.5 rounded-xl bg-gray-100 text-gray-600 hover:bg-gray-200 transition-colors">
            Desactivar
        </button>
        <div class="h-4 w-px bg-gray-200 shrink-0"></div>
        <button type="button"
                @click="if(confirm('Eliminar les plantilles seleccionades?')) submitBulk('delete')"
                class="font-outfit text-xs px-3 py-1.5 rounded-xl bg-red-600 text-white hover:bg-red-700 transition-colors">
            Eliminar
        </button>

    {{-- ── Print Jobs ────────────────────────────────────────────────── --}}
    @elseif($module === 'print_jobs')
        <div class="flex items-center gap-1.5">
            <select x-model="bulkStatus"
                    class="border border-gray-200 rounded-xl px-3 py-1.5 font-outfit text-xs focus:outline-none focus:ring-2 focus:ring-primary/40">
                <option value="">Canviar estat a…</option>
                <option value="in_production">En producció</option>
                <option value="completed">Completat</option>
                <option value="cancelled">Cancel·lat</option>
            </select>
            <button type="button" @click="if(bulkStatus) submitBulk('set_status', {status: bulkStatus})"
                    :disabled="!bulkStatus"
                    class="font-outfit text-xs px-3 py-1.5 rounded-xl bg-primary text-white hover:brightness-110 transition-all disabled:opacity-40 disabled:cursor-not-allowed">
                Aplicar
            </button>
        </div>

    {{-- ── Promo Codes ───────────────────────────────────────────────── --}}
    @elseif($module === 'promo_codes')
        <button type="button" @click="submitBulk('activate')"
                class="font-outfit text-xs px-3 py-1.5 rounded-xl bg-green-50 text-green-700 hover:bg-green-100 transition-colors">
            Activar
        </button>
        <button type="button" @click="submitBulk('deactivate')"
                class="font-outfit text-xs px-3 py-1.5 rounded-xl bg-gray-100 text-gray-600 hover:bg-gray-200 transition-colors">
            Desactivar
        </button>
        <div class="h-4 w-px bg-gray-200 shrink-0"></div>
        <button type="button"
                @click="if(confirm('Eliminar els codis de descompte seleccionats?')) submitBulk('delete')"
                class="font-outfit text-xs px-3 py-1.5 rounded-xl bg-red-600 text-white hover:bg-red-700 transition-colors">
            Eliminar
        </button>

    {{-- ── Reviews ───────────────────────────────────────────────────── --}}
    @elseif($module === 'reviews')
        <button type="button" @click="submitBulk('approve')"
                class="font-outfit text-xs px-3 py-1.5 rounded-xl bg-green-50 text-green-700 hover:bg-green-100 transition-colors">
            Aprovar
        </button>
        <button type="button" @click="submitBulk('reject')"
                class="font-outfit text-xs px-3 py-1.5 rounded-xl bg-orange-50 text-orange-600 hover:bg-orange-100 transition-colors">
            Rebutjar
        </button>
        <div class="h-4 w-px bg-gray-200 shrink-0"></div>
        <button type="button"
                @click="if(confirm('Eliminar les ressenyes seleccionades?')) submitBulk('delete')"
                class="font-outfit text-xs px-3 py-1.5 rounded-xl bg-red-600 text-white hover:bg-red-700 transition-colors">
            Eliminar
        </button>

    {{-- ── Tickets ───────────────────────────────────────────────────── --}}
    @elseif($module === 'tickets')
        <button type="button" @click="submitBulk('close')"
                class="font-outfit text-xs px-3 py-1.5 rounded-xl bg-gray-100 text-gray-600 hover:bg-gray-200 transition-colors">
            Tancar
        </button>
        <button type="button" @click="submitBulk('reopen')"
                class="font-outfit text-xs px-3 py-1.5 rounded-xl bg-blue-50 text-blue-700 hover:bg-blue-100 transition-colors">
            Reobrir
        </button>
        <div class="h-4 w-px bg-gray-200 shrink-0"></div>
        <button type="button"
                @click="if(confirm('Eliminar els tiquets seleccionats?')) submitBulk('delete')"
                class="font-outfit text-xs px-3 py-1.5 rounded-xl bg-red-600 text-white hover:bg-red-700 transition-colors">
            Eliminar
        </button>
    @endif

    {{-- Clear selection --}}
    <button type="button" @click="selected = []; allChecked = false"
            class="ml-auto font-outfit text-xs text-gray-400 hover:text-dark px-3 py-1.5 rounded-xl border border-gray-200 hover:bg-gray-50 transition-colors shrink-0">
        Netejar selecció
    </button>

</div>

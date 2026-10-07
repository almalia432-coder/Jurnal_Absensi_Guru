@auth
@php
    $user = Auth::user();
    $portals = $user->availablePortals();
@endphp
@if(count($portals) > 1)
<div class="portal-switcher-wrap" id="portalSwitcherWrap">
    <button type="button" class="portal-switcher-btn btn-portal-switch" onclick="togglePortalSwitcher(event)" title="Beralih Portal Tugas">
        <i class="fa-solid fa-layer-group" style="color: #4f46e5;"></i>
        <span class="portal-switcher-label">Beralih Portal</span>
        <span class="portal-count-badge">{{ count($portals) }}</span>
        <i class="fa-solid fa-chevron-down arrow-icon" style="font-size: 10px; margin-left: 2px;"></i>
    </button>
    <div class="portal-switcher-dropdown" id="portalSwitcherDropdown">
        <div class="ps-header">
            <span class="ps-title"><i class="fa-solid fa-compass"></i> Portal Anda</span>
            <span class="ps-subtitle">{{ $user->name }}</span>
        </div>
        <div class="ps-list">
            @foreach($portals as $portal)
                @php
                    $isCurrent = request()->routeIs($portal['route']) || request()->is(explode('.', $portal['route'])[0] . '*');
                @endphp
                <a href="{{ route($portal['route']) }}" class="ps-item {{ $isCurrent ? 'active' : '' }}">
                    <div class="ps-icon" style="background-color: {{ $portal['color'] ?? '#3b82f6' }};">
                        <i class="{{ $portal['icon'] ?? 'fa-solid fa-door-open' }}"></i>
                    </div>
                    <div class="ps-item-info">
                        <div class="ps-item-name">
                            {{ $portal['name'] }}
                            @if($isCurrent)
                                <span class="ps-active-tag">Aktif</span>
                            @endif
                        </div>
                        @if(!empty($portal['badge']))
                            <div class="ps-item-badge">
                                <i class="fa-solid fa-clock-rotate-left"></i> {{ $portal['badge'] }}
                            </div>
                        @else
                            <div class="ps-item-desc">{{ $portal['permission'] ?? '' }}</div>
                        @endif
                    </div>
                    <i class="fa-solid fa-arrow-right ps-item-arrow"></i>
                </a>
            @endforeach
        </div>
    </div>
</div>

<style>
.portal-switcher-wrap {
    position: relative;
    display: inline-flex;
    align-items: center;
    margin-right: 12px;
}
.portal-switcher-btn {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    padding: 7px 14px;
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    font-size: 13px;
    font-weight: 600;
    color: #1e293b;
    cursor: pointer;
    transition: all 0.2s ease;
    box-shadow: 0 1px 3px rgba(0,0,0,0.04);
}
.portal-switcher-btn:hover {
    background: #f8fafc;
    border-color: #cbd5e1;
    box-shadow: 0 2px 6px rgba(0,0,0,0.06);
}
.portal-count-badge {
    background: #e0e7ff;
    color: #4338ca;
    font-size: 11px;
    font-weight: 700;
    padding: 1px 6px;
    border-radius: 12px;
}
.portal-switcher-dropdown {
    display: none;
    position: absolute;
    top: calc(100% + 8px);
    right: 0;
    width: 290px;
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 14px;
    box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
    z-index: 1050;
    overflow: hidden;
    animation: psFadeIn 0.18s ease;
}
@keyframes psFadeIn {
    from { opacity: 0; transform: translateY(-6px); }
    to { opacity: 1; transform: translateY(0); }
}
.portal-switcher-dropdown.show {
    display: block;
}
.ps-header {
    padding: 12px 16px;
    background: #f8fafc;
    border-bottom: 1px solid #e2e8f0;
}
.ps-title {
    font-size: 11.5px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: #64748b;
    display: block;
}
.ps-subtitle {
    font-size: 12px;
    color: #0f172a;
    font-weight: 600;
    margin-top: 2px;
    display: block;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.ps-list {
    padding: 8px;
    max-height: 380px;
    overflow-y: auto;
}
.ps-item {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 9px 10px;
    border-radius: 10px;
    text-decoration: none;
    color: #1e293b;
    transition: all 0.15s ease;
    margin-bottom: 4px;
}
.ps-item:last-child {
    margin-bottom: 0;
}
.ps-item:hover {
    background: #f1f5f9;
}
.ps-item.active {
    background: #eff6ff;
    border: 1px solid #bfdbfe;
}
.ps-icon {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #ffffff;
    font-size: 13px;
    flex-shrink: 0;
}
.ps-item-info {
    flex: 1;
    min-width: 0;
}
.ps-item-name {
    font-size: 13px;
    font-weight: 600;
    color: #0f172a;
    display: flex;
    align-items: center;
    gap: 6px;
}
.ps-active-tag {
    font-size: 10px;
    font-weight: 700;
    background: #2563eb;
    color: #ffffff;
    padding: 1px 5px;
    border-radius: 6px;
}
.ps-item-badge {
    font-size: 11px;
    color: #d97706;
    font-weight: 600;
    margin-top: 2px;
}
.ps-item-desc {
    font-size: 11px;
    color: #64748b;
    margin-top: 1px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.ps-item-arrow {
    font-size: 11px;
    color: #94a3b8;
    opacity: 0;
    transition: all 0.15s ease;
}
.ps-item:hover .ps-item-arrow {
    opacity: 1;
    transform: translateX(2px);
}
@media (max-width: 640px) {
    .portal-switcher-label {
        display: none;
    }
}
</style>

<script>
if (typeof togglePortalSwitcher === 'undefined') {
    function togglePortalSwitcher(event) {
        event.stopPropagation();
        const dd = document.getElementById('portalSwitcherDropdown');
        if (dd) {
            dd.classList.toggle('show');
        }
    }
    document.addEventListener('click', function(e) {
        const wrap = document.getElementById('portalSwitcherWrap');
        const dd = document.getElementById('portalSwitcherDropdown');
        if (wrap && dd && !wrap.contains(e.target)) {
            dd.classList.remove('show');
        }
    });
}
</script>
@endif
@endauth

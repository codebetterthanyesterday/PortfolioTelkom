@props([
	'href',
	'active' => false,
	'icon' => null,
	'badge' => null,
	'exact' => false,
])

@php
	// Determine active state if not explicitly passed
	$isActive = $active ?: ($exact
		? request()->fullUrlIs(url($href))
		: request()->is(ltrim(parse_url($href, PHP_URL_PATH), '/')));

	$baseClasses = 'flex items-center gap-3 px-4 py-3 font-medium rounded-lg transition-colors group';
	$colorClasses = $isActive
		? 'bg-[#b01116] text-white'
		: 'text-gray-700 hover:bg-[#b01116] hover:text-white';
	$classes = "$baseClasses $colorClasses";
@endphp

<a href="{{ $href }}" {{ $attributes->merge(['class' => $classes, 'aria-current' => $isActive ? 'page' : false]) }}>
	@if($icon)
		<i class="{{ $icon }} text-xl"></i>
	@endif
	<span>{{ $slot }}</span>
	@if($badge)
		<span class="ml-auto bg-gray-200 group-hover:bg-white/20 text-gray-700 group-hover:text-white text-xs px-2 py-1 rounded-full {{ $isActive ? 'bg-white/20 !text-white' : '' }}">{{ $badge }}</span>
	@endif
</a>

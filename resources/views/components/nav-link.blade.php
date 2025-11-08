@props([
	'href',
	'active' => false,
	'icon' => null,
	'exact' => false,
])

@php
	// Determine active state if not explicitly passed (allow passing boolean or let component compute)
	$isActive = $active ?: ($exact
		? request()->fullUrlIs(url($href))
		: request()->is(ltrim(parse_url($href, PHP_URL_PATH), '/')));

	$baseClasses = 'inline-flex items-center gap-1 px-3 py-2 text-sm font-medium transition-colors duration-200';
	$colorClasses = $isActive
		? 'text-[#b01116] bg-[#b0111614] rounded-md'
		: 'text-gray-700 hover:text-[#b01116] hover:bg-gray-100 rounded-md';
	$classes = "$baseClasses $colorClasses";
@endphp

<a href="{{ $href }}" {{ $attributes->merge(['class' => $classes, 'aria-current' => $isActive ? 'page' : false]) }}>
	@if($icon)
		<i class="{{ $icon }} text-base"></i>
	@endif
	<span>{{ $slot }}</span>
</a>


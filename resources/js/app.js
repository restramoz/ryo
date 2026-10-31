import 'flowbite';
import Alpine from 'alpinejs';

window.Alpine = Alpine;
Alpine.start();

if (document.querySelector('[data-admin-article-form]')) {
    import('./admin-article-form.js').then(({ initializeArticleForm }) => initializeArticleForm());
}

document.querySelectorAll('[data-whatsapp-contact-form]').forEach((form) => {
    form.addEventListener('submit', (event) => {
        event.preventDefault();

        const formData = new FormData(form);
        const message = [
            `Halo Ryo Digital Website, saya ${formData.get('name')}.`,
            formData.get('business') ? `Bisnis/organisasi: ${formData.get('business')}` : null,
            `Kontak: ${formData.get('contact')}`,
            `Layanan: ${formData.get('service')}`,
            `Kebutuhan proyek: ${formData.get('message')}`,
        ].filter(Boolean).join('\n');

        window.open(`https://wa.me/6285648983077?text=${encodeURIComponent(message)}`, '_blank', 'noopener,noreferrer');
    });
});

document.querySelectorAll('[data-regional-service-form]').forEach((form) => {
    const citySelect = form.querySelector('[data-regional-city]');
    const citySearch = form.querySelector('[data-regional-city-search]');
    const provinceSelect = form.querySelector('[data-regional-province]');
    let timer;

    const loadCities = async () => {
        const params = new URLSearchParams();
        if (citySearch.value.trim()) params.set('q', citySearch.value.trim());
        if (provinceSelect.value) params.set('province', provinceSelect.value);
        const response = await fetch(`${form.dataset.citiesUrl}?${params}`, { headers: { Accept: 'application/json' } });
        if (!response.ok) return;
        const { cities } = await response.json();
        citySelect.replaceChildren(new Option('Pilih kota atau kabupaten', ''));
        cities.forEach((city) => citySelect.add(new Option(`${city.name} · ${city.province}`, JSON.stringify(city))));
    };

    citySearch.addEventListener('input', () => { clearTimeout(timer); timer = setTimeout(loadCities, 250); });
    provinceSelect.addEventListener('change', loadCities);
    form.addEventListener('submit', (event) => {
        event.preventDefault();
        if (!form.reportValidity()) return;
        const service = form.elements.namedItem('service');
        const city = JSON.parse(citySelect.value);
        const serviceName = service.options[service.selectedIndex].text;
        const message = `Halo Ryo Digital Website, saya ingin konsultasi tentang ${serviceName} untuk bisnis di ${city.name}, ${city.province}.`;
        window.open(`https://wa.me/6285648983077?text=${encodeURIComponent(message)}`, '_blank', 'noopener,noreferrer');
    });
});

const observer = new IntersectionObserver((entries) => {
    entries.forEach((entry) => {
        if (entry.isIntersecting) {
            entry.target.classList.add('is-visible');
            observer.unobserve(entry.target);
        }
    });
}, { threshold: 0.12 });
document.querySelectorAll('.reveal').forEach((element) => observer.observe(element));

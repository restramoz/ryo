import { Jodit } from 'jodit';
import 'jodit/es2021/jodit.min.css';
import './admin-article-form.css';

const csrfToken = () => document.querySelector('meta[name="csrf-token"]')?.content
    ?? document.querySelector('input[name="_token"]')?.value
    ?? '';

async function postJson(url, payload) {
    const response = await fetch(url, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken() },
        body: JSON.stringify(payload),
    });
    const data = await response.json().catch(() => ({}));
    if (!response.ok || data.success === false) throw new Error(data.message || 'Permintaan belum berhasil. Coba kembali.');
    return data;
}

function setBusy(button, busy, label) {
    if (!button.dataset.label) button.dataset.label = button.innerHTML;
    button.disabled = busy;
    button.innerHTML = busy ? `<span class="inline-block animate-spin">◌</span> ${label}` : button.dataset.label;
}

export function initializeArticleForm() {
    const shell = document.querySelector('[data-admin-article-form]');
    if (!shell) return;

    const saveForm = document.querySelector('[data-article-save-form]');
    const content = shell.parentElement.querySelector('[data-jodit-content]');
    const editor = Jodit.make(content, {
        height: 500,
        minHeight: 360,
        language: 'id',
        placeholder: 'Mulai tulis artikel…',
        toolbarAdaptive: true,
        buttons: 'bold,italic,underline,|,ul,ol,|,paragraph,|,h2,h3,|,blockquote,link,table,|,undo,redo,|,hr,eraser,source',
        uploader: {
            url: shell.dataset.uploadUrl,
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrfToken(), 'Accept': 'application/json' },
            filesVariableName: (index) => `files[${index}]`,
            imagesExtensions: ['jpg', 'jpeg', 'png', 'webp'],
            format: 'json',
            isSuccess: (response) => response.success === true,
            getMessage: (response) => response.message || '',
            process: (response) => ({ files: response.files || response.data?.files || [], baseurl: '', isImages: response.data?.isImages || [true] }),
        },
        style: { color: '#f3f4f2', background: '#171d25' },
    });

    saveForm.addEventListener('submit', () => { content.value = editor.value; });

    const citySelect = shell.querySelector('[data-city-select]');
    const citySearch = shell.querySelector('[data-city-search]');
    const provinceSelect = shell.querySelector('[data-province-select]');
    const districtSelect = shell.querySelector('[data-district-select]');
    const districtSearch = shell.querySelector('[data-district-search]');
    const regionScope = shell.querySelector('[data-region-scope]');
    const regionProvince = shell.querySelector('[data-region-province]');
    const regionCity = shell.querySelector('[data-region-city]');
    const regionDistrict = shell.querySelector('[data-region-district]');
    let cityTimer;
    let districtTimer;
    const updateRegionFields = () => {
        const scope = regionScope.value;
        regionProvince.classList.toggle('hidden', scope === 'all');
        regionCity.classList.toggle('hidden', !['city', 'district'].includes(scope));
        regionDistrict.classList.toggle('hidden', scope !== 'district');
    };
    const searchCities = async () => {
        const params = new URLSearchParams();
        if (citySearch.value.trim()) params.set('q', citySearch.value.trim());
        if (provinceSelect.value) params.set('province', provinceSelect.value);
        const current = citySelect.value;
        try {
            const response = await fetch(`${shell.dataset.citiesUrl}?${params}`, { headers: { Accept: 'application/json' } });
            if (!response.ok) return;
            const { cities } = await response.json();
            citySelect.replaceChildren(new Option('Pilih wilayah (opsional)', ''));
            cities.forEach((city) => citySelect.add(new Option(`${city.name} · ${city.province}`, city.slug)));
            if ([...citySelect.options].some((option) => option.value === current)) citySelect.value = current;
            else if (current) citySelect.add(new Option('Wilayah tersimpan', current, true, true));
        } catch (error) {
            console.error('Regional lookup failed', error);
        }
    };
    const searchDistricts = async () => {
        if (!citySelect.value) return;
        const params = new URLSearchParams({ city: citySelect.value });
        if (districtSearch.value.trim()) params.set('q', districtSearch.value.trim());
        const current = districtSelect.value;
        try {
            const response = await fetch(`${shell.dataset.districtsUrl}?${params}`, { headers: { Accept: 'application/json' } });
            if (!response.ok) return;
            const { districts } = await response.json();
            districtSelect.replaceChildren(new Option('Pilih kecamatan', ''));
            districts.forEach((district) => districtSelect.add(new Option(district.name, district.slug)));
            if ([...districtSelect.options].some((option) => option.value === current)) districtSelect.value = current;
        } catch (error) {
            console.error('Regional district lookup failed', error);
        }
    };
    updateRegionFields();
    regionScope.addEventListener('change', updateRegionFields);
    citySearch.addEventListener('input', () => { clearTimeout(cityTimer); cityTimer = setTimeout(searchCities, 250); });
    districtSearch.addEventListener('input', () => { clearTimeout(districtTimer); districtTimer = setTimeout(searchDistricts, 250); });
    provinceSelect.addEventListener('change', () => {
        citySelect.value = '';
        districtSelect.replaceChildren(new Option('Pilih kecamatan', ''));
        searchCities();
    });
    citySelect.addEventListener('change', () => {
        districtSelect.value = '';
        districtSelect.replaceChildren(new Option('Pilih kecamatan', ''));
        districtSearch.value = '';
        searchDistricts();
    });

    const generateButton = shell.querySelector('[data-generate-article]');
    const generatorStatus = shell.querySelector('[data-generator-status]');
    generateButton.addEventListener('click', async () => {
        setBusy(generateButton, true, 'Menyusun draf');
        generatorStatus.textContent = 'Ollama sedang menyusun artikel bertahap hingga mencapai target kata. Proses 10.000 kata dapat memerlukan waktu cukup lama; biarkan halaman ini tetap terbuka.';
        generatorStatus.classList.remove('text-red-300');
        try {
            const result = await postJson(shell.dataset.generateUrl, {
                topic: shell.querySelector('[data-article-topic]').value,
                word_count: Math.min(10000, Math.max(600, Number(shell.querySelector('[data-word-count]').value) || 1200)),
                service_slug: shell.querySelector('[data-service-select]').value,
                target_region_scope: regionScope.value,
                target_province_slug: provinceSelect.value,
                target_city_slug: citySelect.value,
                target_district_slug: districtSelect.value,
            });
            const assign = (name, value) => { const field = saveForm.elements.namedItem(name); if (field && value !== undefined && value !== null) field.value = value; };
            assign('title', result.title);
            assign('slug', result.slug);
            assign('excerpt', result.excerpt);
            assign('meta_title', result.meta_title);
            assign('meta_description', result.meta_description);
            assign('focus_keyword', result.focus_keyword);
            assign('image_prompt', result.image_prompt);
            assign('image_alt', result.image_alt);
            assign('is_ai_generated', result.is_ai_generated ? '1' : '0');
            editor.value = result.content || '';
            generatorStatus.textContent = 'Draf artikel siap. Periksa isi, data, dan metadata sebelum disimpan atau diterbitkan.';
        } catch (error) {
            generatorStatus.textContent = error.message;
            generatorStatus.classList.add('text-red-300');
        } finally {
            setBusy(generateButton, false);
        }
    });

    const promptButton = shell.querySelector('[data-generate-image-prompt]');
    const promptStatus = shell.querySelector('[data-image-prompt-status]');
    promptButton.addEventListener('click', async () => {
        setBusy(promptButton, true, 'Membuat prompt');
        promptStatus.textContent = '';
        try {
            const result = await postJson(shell.dataset.imagePromptUrl, {
                title: saveForm.elements.namedItem('title').value,
                excerpt: saveForm.elements.namedItem('excerpt').value,
                service_slug: shell.querySelector('[data-service-select]').value,
                target_region_scope: regionScope.value,
                target_province_slug: provinceSelect.value,
                target_city_slug: citySelect.value,
                target_district_slug: districtSelect.value,
            });
            shell.querySelector('[data-image-prompt]').value = result.prompt;
            promptStatus.textContent = 'Prompt gambar sudah dibuat.';
        } catch (error) {
            promptStatus.textContent = error.message;
            promptStatus.classList.add('text-red-300');
        } finally {
            setBusy(promptButton, false);
        }
    });

    shell.querySelector('[data-copy-image-prompt]').addEventListener('click', async () => {
        const prompt = shell.querySelector('[data-image-prompt]').value;
        if (!prompt) { promptStatus.textContent = 'Belum ada prompt untuk disalin.'; return; }
        try {
            await navigator.clipboard.writeText(prompt);
            promptStatus.textContent = 'Prompt disalin ke clipboard.';
        } catch {
            shell.querySelector('[data-image-prompt]').select();
            promptStatus.textContent = 'Prompt dipilih. Salin dengan Ctrl/Cmd + C.';
        }
    });
}

import { ui, defaultLang, languages } from './ui';

export function getLangFromUrl(url: URL) {
    const [, lang, lang2] = url.pathname.split('/');
    if (lang in ui) {
        return lang as keyof typeof ui
    } else if (lang2 in ui) {
        return lang2 as keyof typeof ui
    }
    return defaultLang;
}

export function useTranslations(lang: keyof typeof ui) {
    return function t(key: keyof typeof ui[typeof defaultLang]) {
        return ui[lang][key] || ui[defaultLang][key];
    }
}

export function getLangAndRawIdFromCollectionId(id: string) {
    const splitted = id.split("/")
    if (splitted.length === 0) {
        return
    }

    return {
        "lang": splitted[0],
        "id": splitted[1]
    }
}
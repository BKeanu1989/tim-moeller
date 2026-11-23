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

export const getLangStaticPaths = () =>
  Object.keys(languages).map((lang) => ({ params: { lang } }))

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

export function slugify(text: string) {

    // "Open Source" -> "open-source"
    return text.toString().toLowerCase()
        .replace(/\s+/g, '-')           // Replace spaces with -
        .replace(/[^\w\-]+/g, '')       // Remove all non-word chars
        .replace(/\-\-+/g, '-')         // Replace multiple - with single -
        .replace(/^-+/, '')             // Trim - from start of text
        .replace(/-+$/, '');            // Trim - from end of text
}

export function endSlugify(text: string) {
    // "open-source" -> "Open Source"
    return text.toString().toLowerCase()
        .replace(/-/g, ' ')           // Replace - with space
        .replace(/\b\w/g, (match) => match.toUpperCase()); // Capitalize first letter of each word
    
    
}
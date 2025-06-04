export function buildI18nLink(input: any) {
    // input.slug => "en/somedinges"
    let res = ""
    if (input.slug) {
        let splitted = input.slug.split("/")

        if (splitted.length > 0) {
            res += input.collection + "/" + splitted[1]
        }

    }

    return res
}
import { defineCollection, z } from 'astro:content';

const authorCollection = defineCollection({
    type: 'data',
    schema: ({image}) => z.object({
        name: z.string(),
        link: z.string(),
        // image: image(),
        image: image(),

    })
})

const publicationCollection = defineCollection({
    type: 'content',
    schema: ({ image }) => z.object({
        title: z.string(),
        subTitle: z.string().optional(),
        year: z.number().optional(),
        // text: z.string().optional(),
        image: image(),
        imageAlt: z.string(),
        // image: z.string(),
        authors: z.array(z.string())
    })
})

export const collections = {
    'authors': authorCollection,
    'publications': publicationCollection
}
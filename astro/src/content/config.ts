import { defineCollection, z } from 'astro:content';

const authorCollection = defineCollection({
    type: 'data',
    schema: z.object({
        name: z.string(),
        link: z.string(),
        image: z.string().optional()
    })
})

const publicationCollection = defineCollection({
    type: 'content',
    schema: z.object({
        title: z.string(),
        subTitle: z.string().optional(),
        year: z.number().optional(),
        text: z.string().optional(),
        image: z.string()
    })
})

export const collections = {
    'authors': authorCollection,
    'publications': publicationCollection
}
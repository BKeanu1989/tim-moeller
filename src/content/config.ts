import { file } from 'astro/loaders';
import { defineCollection, z } from 'astro:content'

const authorCollection = defineCollection({
	type: 'data',
	schema: ({ image }) =>
		z.object({
			name: z.string(),
			link: z.string(),
			// image: image(),
			image: image()
		})
})

const publicationCollection = defineCollection({
	type: 'content',
	schema: ({ image }) =>
		z.object({
			title: z.string(),
			subTitle: z.string().optional(),
			year: z.number(),
			// text: z.string().optional(),
			image: image(),
			imageAlt: z.string(),
			// image: z.string(),
			authors: z.array(z.string()),
			tags: z.array(z.string()).optional()
		})
})

const careerCollection = defineCollection({
	loader: file("src/content/career/all.json"),
	schema: ({ image }) => z.object({
		// id: z.string(),
		title: z.string(),
		description: z.string(),
		image: image(),
		imageAlt: z.string(),
		// temperament: z.array(z.string()),
		start: z.number(),
		end: z.number().optional()
	}),
});

export const collections = {
	authors: authorCollection,
	publications: publicationCollection,
	careers: careerCollection,
}

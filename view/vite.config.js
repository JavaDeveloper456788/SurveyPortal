import { defineConfig } from 'vite';

export default defineConfig({
	plugins: [
	],
	publicDir: 'public/',
	envDir: './',
	preview: {
		host: true,
		port: 3000,
	}
});
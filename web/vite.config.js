import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'

import * as path from 'path';
import * as dotenv from 'dotenv';
import * as fs from 'fs';

// https://vitejs.dev/config/
export default defineConfig(({ command, mode }) => {

  let NODE_ENV = process.env.NODE_ENV || 'development'
  let envFiles = [
    `.env.${NODE_ENV}`
  ]
  for (const file of envFiles) {
    const envConfig = dotenv.parse(fs.readFileSync(file))
    for (const k in envConfig) {
      process.env[k] = envConfig[k]
    }
  }

  let alias = {
    '@': path.resolve(__dirname, './src'),
    // 'vue$': 'vue/dist/vue.runtime.esm-bundler.js',
  }

  return {
    base: './', // index.html文件所在位置
    root: './', // js导入的资源路径，src
    resolve: {
      alias,
    },
    define: {
      'process.env': {}
    },
    server: {
      // open: true,
      port: process.env.VITE_CLI_PORT,
      proxy: {
        // 把key的路径代理到target位置
        // detail: https://cli.vuejs.org/config/#devserver-proxy
        [process.env.VITE_BASE_API]: { // 需要代理的路径   例如 '/api'
          target: `${process.env.VITE_BASE_PATH}:${process.env.VITE_SERVER_PORT}/`, // 代理到 目标路径
          changeOrigin: true,
          // rewrite: path => path.replace(new RegExp('^' + process.env.VITE_BASE_API), ''),
        }
      },
    },
    plugins: [
      vue()
    ],
    css: {
      preprocessorOptions: {
        less: {
          // 支持内联 JavaScript
          javascriptEnabled: true,
        }
      }
    },
  }


})

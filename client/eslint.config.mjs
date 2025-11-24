// @ts-check
import withNuxt from './.nuxt/eslint.config.mjs'

export default withNuxt({
  rules: {
    //Запрещает использование console.log() и других методов console
    'no-console': 'error',
    // Запрет alert(), confirm(), prompt()
    'no-alert': 'error',
    //Предупреждает о неиспользуемых переменных в TypeScript
    '@typescript-eslint/no-unused-vars': 'warn',
    //Запрещает использовать тип any в Typescript
    '@typescript-eslint/no-explicit-any': 'off',
    // Контроль комментариев @ts-ignore
    '@typescript-eslint/ban-ts-comment': 'warn',
    // Требует явного указания возвращаемого типа
    '@typescript-eslint/explicit-function-return-type': ['warn', {
      allowExpressions: true
    }],
    // Предупреждение при использовании v-html из-за XSS рисков
    'vue/no-v-html': 'off',
    //Требовать значение по умолчанию для Props
    'vue/require-default-prop': 'warn',
    //Отключает требование многословных имен компонентов 
    'vue/multi-word-component-names': 'off',
    // Разрешает использование двух вариантов <MyComponent></MyComponent> и <MyComponent/>
    'vue/html-self-closing': 'warn',
    //Запрещает использовать имена компонентов в стиле kebab-case
    "vue/component-name-in-template-casing": ["error", "PascalCase", { registeredComponentsOnly: false }],
    // Запрет точек с запятой
    'semi': ['error', 'never'],
    // Отступы в 2 пробела
    'indent': ['error', 2],
    // Запрет висячих запятых
    'comma-dangle': ['error', 'never'],
    // Пробелы внутри фигурных скобок
    'object-curly-spacing': ['error', 'always'],
    // Запрет опасного optional chaining
    'no-unsafe-optional-chaining': 'error',
    // Максимальная длина строки
    'max-len': ['error', {
      code: 120,
      ignoreUrls: true,
      ignoreStrings: true,
      ignoreTemplateLiterals: true
    }]
  }
})

Build/Lint/Test Commands:
1. Build: npm run build or make build (if makefile exists)
2. Lint: npm run lint or eslint .
3. Test: npm run test
4. Single test: npm run test -- --grep "<test name>"

Code Style Guidelines:
5. Use consistent import ordering.
6. Follow Prettier/ESLint formatting rules.
7. Type annotations for clarity.
8. Use descriptive names and camelCase or snake_case as per file context.
9. Handle errors with try/catch and proper logging.
10. Include Cursor and Copilot rules if available from .cursor/rules/ or .github/copilot-instructions.md
# Inertia Axios Overlap Error

**Reproduction repository for Inertia.js race condition bug**

[View the official issue: inertiajs/inertia#3119](https://github.com/inertiajs/inertia/issues/3119)

---

## The Bug

When a form submission overlaps with a background interval request (heartbeat/polling), Inertia triggers `onSuccess` even when the backend returns validation errors (422).

```javascript
// Form POST /tasks (invalid data) → 422 error
// Heartbeat GET /heartbeat (running every 300ms) → 200 success
// Result: onSuccess callback fires ❌ (should be onError)
```

**Impact**: User thinks the task is saved, but data was never saved.

---

## How to Reproduce

1. **Background Heartbeat**: Polling request every 300ms
2. **Form Submission**: Submit with invalid data (missing required fields)
3. **Timing**: Wait for requests to overlap
4. **Result**: `onSuccess` fires despite validation errors

See `/inertia-1.0/README.md` or `/inertia-2.0/README.md` for setup instructions.

---

## Workaround

Check for errors inside `onSuccess`:

```javascript
form.post('/tasks', {
    onSuccess: () => {
        if (form.hasErrors) {
            // Handle errors even though onSuccess was called
            showErrorMessage();
        } else {
            // True success
            showSuccessMessage();
        }
    }
});
```

---

## Versions Tested

- Inertia: v1.x, v2.x, v3 beta
- Backend: Laravel 11.1+
- Issue persists across all versions

---

**Issue Link**: https://github.com/inertiajs/inertia/issues/3119

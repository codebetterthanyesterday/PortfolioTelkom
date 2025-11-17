# Live Search Feature Documentation

## Overview
The live search feature provides real-time search across Students, Projects, and Investors with optimized database queries and a responsive user interface.

## Features Implemented

### 1. Student Search
- **Search by Username**: Find students by their unique username
- **Search by Full Name**: Search using complete or partial names
- **Search by Email**: Look up students by email address
- **Search by Student ID**: Find specific students using their student ID
- **Search by Expertise**: Discover students based on their areas of expertise

### 2. Project Search
- **Search by Title**: Find projects by name/title
- **Search by Category**: Discover projects within specific categories
- **Search by Subject**: Find projects related to specific academic subjects
- **Search by Teacher**: Locate projects supervised by specific teachers
- **Search by Student (Owner/Contributor)**: Find projects by owner or team members

### 3. Investor Search
- **Search by Email**: Find investors by email address
- **Search by Company**: Locate investors by company name
- **Search by Industry**: Discover investors in specific industries
- **Search by Username**: Find investors by username
- **Search by Full Name**: Search for investors by name

## API Endpoints

### Live Search API
```
GET /api/search?q={search_term}&limit={limit}
```

**Parameters:**
- `q` (required): Search query string (minimum 2 characters)
- `limit` (optional): Number of results per category (default: 10)

**Response:**
```json
{
    "success": true,
    "results": {
        "students": [
            {
                "id": 1,
                "student_id": "STD001",
                "username": "johndoe",
                "full_name": "John Doe",
                "email": "john@example.com",
                "avatar": "http://example.com/storage/avatars/avatar.jpg",
                "profile_url": "http://example.com/students/johndoe",
                "created_at": "2024-01-01T00:00:00.000000Z"
            }
        ],
        "projects": [
            {
                "id": 1,
                "title": "AI Project",
                "slug": "ai-project",
                "price": 5000000,
                "formatted_price": "Rp 5.000.000",
                "type": "individual",
                "view_count": 125,
                "owner": {
                    "student_id": 1,
                    "username": "johndoe",
                    "full_name": "John Doe"
                },
                "categories": ["Technology", "AI"],
                "thumbnail": "http://example.com/storage/projects/thumb.jpg",
                "url": "http://example.com/projects/ai-project",
                "created_at": "2024-01-01T00:00:00.000000Z"
            }
        ],
        "investors": [
            {
                "id": 1,
                "username": "investor1",
                "full_name": "Jane Smith",
                "email": "jane@company.com",
                "company_name": "Tech Ventures",
                "industry": "Technology",
                "avatar": "http://example.com/storage/avatars/avatar.jpg",
                "wishlist_count": 5,
                "created_at": "2024-01-01T00:00:00.000000Z"
            }
        ]
    },
    "counts": {
        "students": 1,
        "projects": 1,
        "investors": 1,
        "total": 3
    }
}
```

### Advanced Search API
```
POST /api/search/advanced
```

**Request Body:**
```json
{
    "query": "search term",
    "type": "students|projects|investors|all",
    "category_id": 1,
    "subject_id": 2,
    "teacher_id": 3,
    "expertise_id": 4,
    "project_type": "individual|team",
    "industry": "Technology",
    "sort": "relevance|name|date|views",
    "per_page": 20
}
```

## Performance Optimizations

### Database Indexes
The following indexes have been added for optimal search performance:

#### Users Table
- `username` index
- `full_name` index
- `email` index (existing)
- `role` index
- Composite index on `(username, full_name, email)`

#### Students Table
- `student_id` index
- `user_id` index (foreign key)

#### Investors Table
- `company_name` index
- `industry` index
- `user_id` index (foreign key)

#### Projects Table
- `title` index
- `status` index
- `type` index
- `student_id` index (foreign key)
- `view_count` index
- Composite index on `(status, type)`

#### Categories, Subjects, Teachers, Expertises
- `name` index on all tables
- `code` index on subjects
- `email` index on teachers
- `institution` index on teachers

#### Junction Tables (for efficient joins)
- All foreign key columns indexed
- `project_category`, `project_subject`, `project_teacher`
- `student_expertise`, `project_members`, `wishlists`

### Query Optimization Techniques

1. **Selective Field Retrieval**: Only necessary columns are selected
2. **Optimized Joins**: Uses LEFT JOINs efficiently for optional relationships
3. **DISTINCT Queries**: Prevents duplicate results from multiple joins
4. **Relevance Sorting**: Results ordered by search relevance
5. **Early Filtering**: Status and permission checks applied early
6. **Limit Controls**: Built-in result limits prevent large datasets

## Frontend Integration

### Alpine.js Implementation
The search functionality is integrated into the navbar using Alpine.js with:

- **Debounced Input**: 300ms delay to reduce API calls
- **Loading States**: Visual feedback during search
- **Results Categorization**: Organized display by type
- **Mobile Responsive**: Optimized for all screen sizes
- **Click-away Handling**: Automatic close on outside clicks

### Usage in Navbar
```html
<div x-data="{
    searchOpen: false,
    searchQuery: '',
    searchResults: { students: [], projects: [], investors: [] },
    searchCounts: { students: 0, projects: 0, investors: 0, total: 0 },
    loading: false,
    
    async search() { /* implementation */ }
}">
    <!-- Search UI -->
</div>
```

## Error Handling

### Backend Error Responses
```json
{
    "success": false,
    "error": "Search failed. Please try again.",
    "debug": "Detailed error message (only in debug mode)"
}
```

### Frontend Error Handling
- Network errors caught and logged
- Graceful fallback for API failures
- User-friendly error messages

## Security Considerations

1. **Input Validation**: Minimum 2 characters required
2. **SQL Injection Protection**: Uses Laravel's query builder with parameter binding
3. **Rate Limiting**: Can be added via Laravel middleware
4. **Permission Checks**: Only published projects are searchable
5. **Data Sanitization**: Output properly escaped in frontend

## Usage Examples

### Basic Search
```javascript
// Search for "john"
fetch('/api/search?q=john')
    .then(response => response.json())
    .then(data => console.log(data));
```

### Advanced Search
```javascript
// Search for AI projects
fetch('/api/search/advanced', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
    },
    body: JSON.stringify({
        query: 'artificial intelligence',
        type: 'projects',
        category_id: 1,
        sort: 'relevance'
    })
}).then(response => response.json())
  .then(data => console.log(data));
```

## File Structure

```
app/Http/Controllers/
├── SearchController.php          # Main search logic

resources/views/components/
├── navbar.blade.php              # Frontend search UI

database/migrations/
├── add_search_indexes_to_tables.php  # Performance indexes

routes/
├── web.php                       # Search route definitions
```

## Testing

### Manual Testing
1. Start the Laravel server: `php artisan serve`
2. Navigate to the website
3. Click the search button in the navbar
4. Type at least 2 characters to trigger search
5. Verify results appear correctly

### API Testing
```bash
# Test basic search
curl "http://localhost:8000/api/search?q=test"

# Test advanced search
curl -X POST "http://localhost:8000/api/search/advanced" \
     -H "Content-Type: application/json" \
     -d '{"query":"test","type":"all"}'
```

## Future Enhancements

1. **Full-Text Search**: Implement PostgreSQL full-text search for better relevance
2. **Search Analytics**: Track popular search terms and results
3. **Autocomplete**: Add search suggestions and autocomplete functionality
4. **Search History**: Store user search history
5. **Filters UI**: Add visual filter interface for advanced search
6. **Search Highlighting**: Highlight matching terms in results
7. **Faceted Search**: Add faceted navigation for refined searches

## Troubleshooting

### Common Issues

1. **No Results**: Check database has data and indexes are created
2. **Slow Performance**: Verify indexes are properly applied
3. **JavaScript Errors**: Check browser console for Alpine.js errors
4. **API Errors**: Check Laravel logs in `storage/logs/`

### Debugging Commands
```bash
# Check if indexes exist
php artisan db:show --database=pgsql

# Clear cache
php artisan cache:clear
php artisan config:clear

# Check routes
php artisan route:list | grep search
```
# Enhanced Project Creation Modals - Implementation Guide

This guide contains the enhanced code for the project creation modals with all requested features:

## Features Implemented:

1. ✅ **Real-time Search** for categories, subjects, teachers, and team members
2. ✅ **Create New** functionality - Add categories, subjects, teachers directly from modal
3. ✅ **Team Project** now includes subjects and teachers selection
4. ✅ **Enhanced UI Design** - Modern, interactive, clean, and elegant
5. ✅ **Position Fields** for team members with individual inputs
6. ✅ **Backend API Routes** for dynamic data creation

## Backend Setup (Already Completed):

### Routes Added (`routes/web.php`):
```php
Route::post('/categories', [StudentController::class, 'storeCategory'])->name('categories.store');
Route::post('/subjects', [StudentController::class, 'storeSubject'])->name('subjects.store');
Route::post('/teachers', [StudentController::class, 'storeTeacher'])->name('teachers.store');
```

### Controller Methods Added (`StudentController.php`):
- `storeCategory()` - Create new category
- `storeSubject()` - Create new subject  
- `storeTeacher()` - Create new teacher

### CSRF Token Meta Tag Added (`layout.blade.php`):
```html
<meta name="csrf-token" content="{{ csrf_token() }}">
```

## Frontend Implementation:

### Key Alpine.js Data Properties Added:

```javascript
// Search filters
searchCategory: '',
searchSubject: '',
searchTeacher: '',
searchStudent: '',

// Show add new forms
showAddCategory: false,
showAddSubject: false,
showAddTeacher: false,

// New data forms
newCategory: { name: '', description: '' },
newSubject: { name: '', code: '', description: '' },
newTeacher: { name: '', nip: '', email: '', phone_number: '', institution: '' },

// Store all available data
availableCategories: @js($categories),
availableSubjects: @js($subjects),
availableTeachers: @js($teachers),
availableStudents: @js($students),
```

### Filter Functions (Computed Properties):

```javascript
get filteredCategories() {
    if (!this.searchCategory) return this.availableCategories;
    return this.availableCategories.filter(cat => 
        cat.name.toLowerCase().includes(this.searchCategory.toLowerCase())
    );
},
get filteredSubjects() {
    if (!this.searchSubject) return this.availableSubjects;
    return this.availableSubjects.filter(sub => 
        sub.name.toLowerCase().includes(this.searchSubject.toLowerCase()) ||
        (sub.code && sub.code.toLowerCase().includes(this.searchSubject.toLowerCase()))
    );
},
get filteredTeachers() {
    if (!this.searchTeacher) return this.availableTeachers;
    return this.availableTeachers.filter(teacher => 
        teacher.name.toLowerCase().includes(this.searchTeacher.toLowerCase()) ||
        (teacher.nip && teacher.nip.toLowerCase().includes(this.searchTeacher.toLowerCase()))
    );
},
get filteredStudents() {
    if (!this.searchStudent) return this.availableStudents;
    return this.availableStudents.filter(student => 
        (student.user.full_name && student.user.full_name.toLowerCase().includes(this.searchStudent.toLowerCase())) ||
        student.user.username.toLowerCase().includes(this.searchStudent.toLowerCase()) ||
        (student.student_id && student.student_id.toLowerCase().includes(this.searchStudent.toLowerCase()))
    );
}
```

### Create Functions (API Calls):

```javascript
async createCategory() {
    if (!this.newCategory.name) return;
    try {
        const response = await fetch('/student/categories', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify(this.newCategory)
        });
        const data = await response.json();
        if (data.success) {
            this.availableCategories.push(data.category);
            this.projectData.categories.push(data.category.id);
            this.newCategory = { name: '', description: '' };
            this.showAddCategory = false;
            this.searchCategory = '';
        }
    } catch (error) {
        console.error('Error creating category:', error);
    }
}
// Similar functions for createSubject() and createTeacher()
```

## Enhanced Modal Components:

### 1. Search Box Component (Reusable):
```html
<!-- Search Input with Icon -->
<div class="relative mb-4">
    <i class="ri-search-line absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
    <input 
        type="text" 
        x-model="searchCategory"
        placeholder="Cari kategori..."
        class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#b01116] focus:border-transparent transition-all">
</div>
```

### 2. "Create New" Button:
```html
<button 
    type="button"
    @click="showAddCategory = !showAddCategory"
    class="w-full py-2.5 border-2 border-dashed border-gray-300 rounded-lg text-gray-600 hover:border-[#b01116] hover:text-[#b01116] transition-all flex items-center justify-center gap-2 font-medium">
    <i class="ri-add-circle-line text-xl"></i>
    <span x-text="showAddCategory ? 'Batal Tambah' : 'Tambah Kategori Baru'"></span>
</button>
```

### 3. Inline Create Form:
```html
<div x-show="showAddCategory" x-transition class="mt-3 p-4 bg-blue-50 rounded-lg border border-blue-200">
    <h5 class="font-medium text-gray-800 mb-3 flex items-center gap-2">
        <i class="ri-add-line text-[#b01116]"></i>
        Tambah Kategori Baru
    </h5>
    <div class="space-y-3">
        <input 
            type="text" 
            x-model="newCategory.name"
            placeholder="Nama Kategori *"
            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#b01116]">
        <textarea 
            x-model="newCategory.description"
            placeholder="Deskripsi (opsional)"
            rows="2"
            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#b01116]"></textarea>
        <button 
            type="button"
            @click="createCategory()"
            class="w-full bg-[#b01116] hover:bg-[#8d0d11] text-white py-2 rounded-lg font-medium transition-colors flex items-center justify-center gap-2">
            <i class="ri-save-line"></i>
            Simpan Kategori
        </button>
    </div>
</div>
```

### 4. Enhanced Selection Grid with Search Results:
```html
<div class="grid grid-cols-2 md:grid-cols-3 gap-3 max-h-64 overflow-y-auto">
    <template x-for="category in filteredCategories" :key="category.id">
        <label 
            class="flex items-center p-3 border-2 rounded-lg cursor-pointer hover:shadow-md transition-all duration-200"
            :class="projectData.categories.includes(category.id) ? 'border-[#b01116] bg-red-50 shadow-sm' : 'border-gray-200 hover:border-gray-300'">
            <input 
                type="checkbox" 
                class="sr-only" 
                :checked="projectData.categories.includes(category.id)"
                @change="toggleCategory(category.id)">
            <div 
                class="w-5 h-5 rounded border-2 mr-3 flex items-center justify-center transition-all"
                :class="projectData.categories.includes(category.id) ? 'bg-[#b01116] border-[#b01116] scale-110' : 'border-gray-300'">
                <i class="ri-check-line text-white text-sm font-bold" x-show="projectData.categories.includes(category.id)"></i>
            </div>
            <span 
                class="text-sm font-medium transition-colors"
                :class="projectData.categories.includes(category.id) ? 'text-[#b01116]' : 'text-gray-700'"
                x-text="category.name"></span>
        </label>
    </template>
</div>

<!-- No Results Message -->
<div x-show="filteredCategories.length === 0" class="text-center py-8 text-gray-500">
    <i class="ri-search-line text-4xl mb-2"></i>
    <p>Tidak ada kategori yang sesuai</p>
    <p class="text-sm mt-1">Coba kata kunci lain atau tambahkan kategori baru</p>
</div>
```

### 5. Team Member Selection with Position Input:
```html
<template x-for="student in filteredStudents" :key="student.id">
    <div 
        class="p-4 border-2 rounded-lg transition-all duration-200"
        :class="projectData.team_members.includes(student.id) ? 'border-[#b01116] bg-red-50' : 'border-gray-200 hover:border-gray-300'">
        
        <!-- Student Info with Checkbox -->
        <label class="flex items-start gap-3 cursor-pointer">
            <input 
                type="checkbox" 
                class="sr-only" 
                :checked="projectData.team_members.includes(student.id)"
                @change="toggleTeamMember(student.id)">
            
            <div 
                class="w-5 h-5 rounded border-2 flex-shrink-0 mt-1 flex items-center justify-center transition-all"
                :class="projectData.team_members.includes(student.id) ? 'bg-[#b01116] border-[#b01116]' : 'border-gray-300'">
                <i class="ri-check-line text-white text-sm" x-show="projectData.team_members.includes(student.id)"></i>
            </div>
            
            <div class="flex items-center gap-3 flex-1">
                <!-- Avatar -->
                <template x-if="student.user.avatar">
                    <img :src="student.user.avatar_url" alt="Avatar" class="w-12 h-12 rounded-full object-cover ring-2 ring-gray-200">
                </template>
                <template x-if="!student.user.avatar">
                    <div class="w-12 h-12 rounded-full bg-gradient-to-br from-[#b01116] to-pink-600 flex items-center justify-center text-white text-lg font-bold ring-2 ring-gray-200">
                        <span x-text="student.user.username.charAt(0).toUpperCase()"></span>
                    </div>
                </template>
                
                <!-- Student Details -->
                <div class="flex-1">
                    <div class="font-semibold text-gray-800" x-text="student.user.full_name || student.user.username"></div>
                    <div class="text-sm text-gray-600">@<span x-text="student.user.username"></span></div>
                    <template x-if="student.student_id">
                        <div class="text-xs text-gray-500 mt-0.5">NIM: <span x-text="student.student_id"></span></div>
                    </template>
                </div>
            </div>
        </label>
        
        <!-- Position Input (Shows when selected) -->
        <div 
            x-show="projectData.team_members.includes(student.id)" 
            x-transition
            class="mt-3 pl-8">
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Posisi dalam Tim *</label>
            <input 
                type="text" 
                x-model="projectData.team_positions[student.id]"
                placeholder="Contoh: Frontend Developer, UI Designer, Project Manager"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#b01116] focus:border-transparent text-sm">
            <p class="text-xs text-gray-500 mt-1">
                <i class="ri-information-line"></i>
                Jelaskan peran spesifik anggota tim ini
            </p>
        </div>
    </div>
</template>
```

### 6. Enhanced Progress Indicator:
```html
<div class="flex items-center justify-center gap-2">
    <template x-for="step in totalSteps" :key="step">
        <div class="flex items-center gap-2">
            <!-- Step Circle -->
            <div 
                class="relative w-10 h-10 rounded-full flex items-center justify-center text-sm font-bold transition-all duration-300"
                :class="step < currentStep ? 'bg-green-500 text-white' : step === currentStep ? 'bg-[#b01116] text-white ring-4 ring-red-100' : 'bg-gray-200 text-gray-500'">
                <span x-show="step < currentStep">
                    <i class="ri-check-line text-lg"></i>
                </span>
                <span x-show="step >= currentStep" x-text="step"></span>
            </div>
            
            <!-- Connector Line -->
            <div 
                x-show="step < totalSteps"
                class="w-16 h-1 transition-all duration-300 rounded-full" 
                :class="step < currentStep ? 'bg-green-500' : 'bg-gray-200'"></div>
        </div>
    </template>
</div>
```

### 7. Selection Counter Badge:
```html
<div class="mt-4 flex items-center gap-4">
    <!-- Selected Count -->
    <div class="flex-1 p-3 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg border border-blue-200">
        <div class="flex items-center gap-2 text-blue-700">
            <div class="w-8 h-8 rounded-full bg-blue-500 text-white flex items-center justify-center font-bold">
                <span x-text="projectData.categories.length"></span>
            </div>
            <span class="text-sm font-medium">Kategori Dipilih</span>
        </div>
    </div>
    
    <!-- Requirement Info -->
    <div class="flex items-center gap-2 text-sm text-gray-600">
        <i class="ri-information-line text-gray-400"></i>
        <span>Minimal 1 kategori wajib dipilih</span>
    </div>
</div>
```

## Step 2 Enhancement for Team Project Modal:

Add subjects and teachers selection (similar to individual project):

```html
<!-- Step 2: Categories, Subjects & Teachers -->
<div x-show="currentStep === 2" x-transition class="p-6 space-y-8">
    <h3 class="text-xl font-bold text-gray-800 flex items-center gap-2">
        <i class="ri-stack-line text-[#b01116]"></i>
        Pilih Kategori, Mata Kuliah & Dosen
    </h3>
    
    <!-- Categories Section -->
    <div>
        <h4 class="font-semibold text-gray-800 mb-3 flex items-center gap-2">
            <i class="ri-price-tag-3-line"></i>
            Kategori Proyek *
        </h4>
        <!-- Search Box -->
        <div class="relative mb-4">
            <i class="ri-search-line absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
            <input 
                type="text" 
                x-model="searchCategory"
                placeholder="Cari kategori..."
                class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#b01116]">
        </div>
        <!-- Selection Grid -->
        <!-- ... (as shown above) -->
        <!-- Add New Button -->
        <!-- ... (as shown above) -->
    </div>
    
    <!-- Subjects Section (Same Pattern) -->
    <div>
        <h4 class="font-semibold text-gray-800 mb-3 flex items-center gap-2">
            <i class="ri-book-open-line"></i>
            Mata Kuliah (Opsional)
        </h4>
        <!-- ... Similar structure as categories -->
    </div>
    
    <!-- Teachers Section (Same Pattern) -->
    <div>
        <h4 class="font-semibold text-gray-800 mb-3 flex items-center gap-2">
            <i class="ri-user-star-line"></i>
            Dosen/Guru Pembimbing (Opsional)
        </h4>
        <!-- ... Similar structure as categories -->
    </div>
</div>
```

## Updated resetProjectModal Function:

```javascript
resetProjectModal() {
    this.currentStep = 1;
    this.projectData = {
        title: '',
        description: '',
        price: '',
        status: 'draft',
        categories: [],
        subjects: [],
        teachers: [],
        team_members: [],
        team_positions: {}
    };
    this.selectedFiles = [];
    this.searchCategory = '';
    this.searchSubject = '';
    this.searchTeacher = '';
    this.searchStudent = '';
    this.showAddCategory = false;
    this.showAddSubject = false;
    this.showAddTeacher = false;
    this.newCategory = { name: '', description: '' };
    this.newSubject = { name: '', code: '', description: '' };
    this.newTeacher = { name: '', nip: '', email: '', phone_number: '', institution: '' };
}
```

## UI/UX Enhancements Applied:

1. **Smooth Transitions** - All show/hide uses `x-transition`
2. **Hover Effects** - Scale, shadow, and color changes on hover
3. **Visual Feedback** - Selected items have distinct colors and borders
4. **Icons** - Remix Icon throughout for better visual communication
5. **Gradient Backgrounds** - Subtle gradients for info boxes
6. **Ring Effects** - Focus rings for better accessibility
7. **Badges & Counters** - Visual indicators for selection counts
8. **Search Icons** - Positioned inside input fields
9. **Checkmarks** - Animated checkmarks for completed steps
10. **Spacing** - Consistent padding and gaps for clean layout

## File Locations:

- `routes/web.php` - ✅ Updated
- `app/Http/Controllers/StudentController.php` - ✅ Updated
- `resources/views/layout/layout.blade.php` - ✅ Updated (CSRF token)
- `resources/views/pages/student/profile.blade.php` - ⏳ Needs manual update

## Next Steps:

Apply these enhancements to the `profile.blade.php` file. The modals should be replaced entirely with the enhanced versions that include:
- Search functionality
- Create new inline forms
- Enhanced UI styling
- Proper Alpine.js data bindings

Would you like me to generate the complete updated profile.blade.php file or specific sections?

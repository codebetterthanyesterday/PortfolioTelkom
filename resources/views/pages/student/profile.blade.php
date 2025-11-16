@extends('layout.layout')

@section('title', "Profil Saya")

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    <div class="flex flex-col lg:flex-row gap-8" 
        x-data="{
            activeTab: 'all',
            showEditModal: false,
            showIndividualProjectModal: false,
            showTeamProjectModal: false,
            showEditProjectModal: false,
            editingProject: null,

            // Shared step state (used by all modals)
            currentStep: 1,
            totalSteps: 3,

            // Profile data
            selectedExpertises: @js(auth()->user()->student && auth()->user()->student->expertises ? auth()->user()->student->expertises->pluck('id') : []),
            education: @js(auth()->user()->student && auth()->user()->student->educationInfo ? auth()->user()->student->educationInfo : []),
            avatarPreview: null,

            // Project creation data
            projectType: 'individual',
            projectData: {
                title: '',
                description: '',
                price: '',
                status: 'draft',
                categories: [],
                subjects: [],
                teachers: [],
                team_members: [],
                team_positions: {}
            },
            originalProjectData: null,
            originalTeamPositions: null,

            // Search filters
            searchCategory: '',
            searchSubject: '',
            searchTeacher: '',
            searchStudent: '',
            searchExpertise: '',

            // Toggle add forms
            showAddCategory: false,
            showAddSubject: false,
            showAddTeacher: false,
            showAddExpertise: false,

            // New data forms
            newCategory: { name: '', description: '' },
            newSubject: { name: '', code: '', description: '' },
            newTeacher: { name: '', nip: '', email: '', phone_number: '', institution: '' },
            newExpertise: { name: '' },

            // Available data
            availableCategories: @js($categories),
            availableSubjects: @js($subjects),
            availableTeachers: @js($teachers),
            availableStudents: @js($students),
            availableExpertises: @js($expertises ?? []),

            selectedFiles: [],
            newEducation: {
                institution_name: '',
                degree: '',
                field_of_study: '',
                start_date: '',
                end_date: '',
                is_current: false,
                description: ''
            },

            // Computed filters
            get filteredCategories() {
                return !this.searchCategory
                    ? this.availableCategories
                    : this.availableCategories.filter(c => c.name.toLowerCase().includes(this.searchCategory.toLowerCase()));
            },
            get filteredSubjects() {
                return !this.searchSubject
                    ? this.availableSubjects
                    : this.availableSubjects.filter(s =>
                        s.name.toLowerCase().includes(this.searchSubject.toLowerCase()) ||
                        (s.code && s.code.toLowerCase().includes(this.searchSubject.toLowerCase()))
                    );
            },
            get filteredTeachers() {
                return !this.searchTeacher
                    ? this.availableTeachers
                    : this.availableTeachers.filter(t =>
                        t.name.toLowerCase().includes(this.searchTeacher.toLowerCase()) ||
                        (t.nip && t.nip.toLowerCase().includes(this.searchTeacher.toLowerCase()))
                    );
            },
            get filteredStudents() {
                return !this.searchStudent
                    ? this.availableStudents
                    : this.availableStudents.filter(st =>
                        (st.user.full_name && st.user.full_name.toLowerCase().includes(this.searchStudent.toLowerCase())) ||
                        st.user.username.toLowerCase().includes(this.searchStudent.toLowerCase()) ||
                        (st.student_id && st.student_id.toLowerCase().includes(this.searchStudent.toLowerCase()))
                    );
            },
            get filteredExpertises() {
                return !this.searchExpertise
                    ? this.availableExpertises
                    : this.availableExpertises.filter(e => 
                        e.name.toLowerCase().includes(this.searchExpertise.toLowerCase())
                    );
            },

            // Create new entities
            async createCategory() {
                if (!this.newCategory.name.trim()) { alert('Nama kategori harus diisi'); return; }
                await this.postJSON('/student/categories', this.newCategory, (data) => {
                    this.availableCategories.push(data.category);
                    this.projectData.categories.push(data.category.id);
                    this.newCategory = { name: '', description: '' };
                    this.showAddCategory = false;
                    this.searchCategory = '';
                    alert('Kategori berhasil ditambahkan!');
                }, 'Gagal menambahkan kategori');
            },
            async createSubject() {
                if (!this.newSubject.name.trim()) { alert('Nama mata kuliah harus diisi'); return; }
                await this.postJSON('/student/subjects', this.newSubject, (data) => {
                    this.availableSubjects.push(data.subject);
                    this.projectData.subjects.push(data.subject.id);
                    this.newSubject = { name: '', code: '', description: '' };
                    this.showAddSubject = false;
                    this.searchSubject = '';
                    alert('Mata kuliah berhasil ditambahkan!');
                }, 'Gagal menambahkan mata kuliah');
            },
            async createTeacher() {
                if (!this.newTeacher.name.trim()) { alert('Nama dosen/guru harus diisi'); return; }
                await this.postJSON('/student/teachers', this.newTeacher, (data) => {
                    this.availableTeachers.push(data.teacher);
                    this.projectData.teachers.push(data.teacher.id);
                    this.newTeacher = { name: '', nip: '', email: '', phone_number: '', institution: '' };
                    this.showAddTeacher = false;
                    this.searchTeacher = '';
                    alert('Dosen/Guru berhasil ditambahkan!');
                }, 'Gagal menambahkan dosen/guru');
            },
            async createExpertise() {
                if (!this.newExpertise.name.trim()) { alert('Nama keahlian harus diisi'); return; }
                await this.postJSON('/student/expertises', this.newExpertise, (data) => {
                    this.availableExpertises.push(data.expertise);
                    this.selectedExpertises.push(data.expertise.id);
                    this.newExpertise = { name: '' };
                    this.showAddExpertise = false;
                    this.searchExpertise = '';
                    alert('Keahlian berhasil ditambahkan!');
                }, 'Gagal menambahkan keahlian');
            },
            async postJSON(url, payload, onSuccess, failMsg) {
                try {
                    const res = await fetch(url, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                        },
                        body: JSON.stringify(payload)
                    });
                    if (!res.ok) throw new Error('Network');
                    const data = await res.json();
                    if (data.success) { onSuccess(data); } else { alert(failMsg); }
                } catch (e) {
                    console.error(e);
                    alert('Terjadi kesalahan.');
                }
            },

            // Validation functions
            validateStep1() {
                return this.projectData.title.trim() !== '' && 
                       this.projectData.status !== '';
            },
            validateStep2() {
                return this.projectData.categories.length > 0;
            },
            validateStep3() {
                // For team projects, require at least 1 team member (+ leader = 2 total)
                if (this.showTeamProjectModal) {
                    return this.projectData.team_members.length >= 1;
                }
                // For individual projects, step 3 is always valid
                return true;
            },
            canProceedToNext() {
                // For edit profile modal, always allow proceeding to next step
                if (this.showEditModal) {
                    return true;
                }
                // For project modals, use validation
                switch(this.currentStep) {
                    case 1: return this.validateStep1();
                    case 2: return this.validateStep2();
                    case 3: return this.validateStep3();
                    default: return false;
                }
            },
            canCreateProject() {
                // All steps must be valid to create project
                return this.validateStep1() && this.validateStep2() && this.validateStep3();
            },
            getValidationMessage() {
                // No validation messages for edit profile modal
                if (this.showEditModal) {
                    return '';
                }
                // For project modals, show validation messages
                switch(this.currentStep) {
                    case 1: 
                        if (!this.projectData.title.trim()) return 'Judul proyek wajib diisi';
                        if (!this.projectData.status) return 'Status publikasi wajib dipilih';
                        break;
                    case 2:
                        if (this.projectData.categories.length === 0) return 'Pilih minimal 1 kategori proyek';
                        break;
                    case 3:
                        if (this.showTeamProjectModal && this.projectData.team_members.length < 1) 
                            return 'Pilih minimal 1 anggota tim (selain leader)';
                        break;
                    default: return '';
                }
                return '';
            },

            // Steps
            nextStep() { 
                if (!this.canProceedToNext()) {
                    alert(this.getValidationMessage());
                    return;
                }
                if (this.currentStep < this.totalSteps) this.currentStep++; 
            },
            prevStep() { if (this.currentStep > 1) this.currentStep--; },

            // Reset profile modal
            resetModal() {
                this.currentStep = 1;
                this.selectedExpertises = @js(auth()->user()->student && auth()->user()->student->expertises ? auth()->user()->student->expertises->pluck('id') : []);
                this.education = @js(auth()->user()->student && auth()->user()->student->educationInfo ? auth()->user()->student->educationInfo : []);
                this.searchExpertise = '';
                this.showAddExpertise = false;
                this.newExpertise = { name: '' };
                this.avatarPreview = null;
            },
            
            // Handle avatar preview
            handleAvatarPreview(event) {
                const file = event.target.files[0];
                if (file) {
                    if (file.size > 2 * 1024 * 1024) {
                        alert('Ukuran file terlalu besar. Maksimal 2MB.');
                        event.target.value = '';
                        this.avatarPreview = null;
                        return;
                    }
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        this.avatarPreview = e.target.result;
                    };
                    reader.readAsDataURL(file);
                } else {
                    this.avatarPreview = null;
                }
            },

            // Reset project modals
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
                this.editingProject = null;
                this.originalProjectData = null;
                this.originalTeamPositions = null;
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
                this.newExpertise = { name: '' };
            },

            // Change detection helpers
            hasChanged(field) {
                if (!this.originalProjectData) return false;
                if (Array.isArray(this.projectData[field])) {
                    const original = (this.originalProjectData[field] || []).sort().join(',');
                    const current = (this.projectData[field] || []).sort().join(',');
                    return original !== current;
                }
                return this.projectData[field] !== this.originalProjectData[field];
            },
            getChangedValue(field) {
                if (!this.originalProjectData) return this.projectData[field];
                if (Array.isArray(this.projectData[field])) {
                    return this.projectData[field].length;
                }
                return this.projectData[field];
            },
            getOriginalValue(field) {
                if (!this.originalProjectData) return null;
                if (Array.isArray(this.originalProjectData[field])) {
                    return this.originalProjectData[field].length;
                }
                return this.originalProjectData[field];
            },

            // Load project data for editing
            async loadProjectForEdit(projectId) {
                try {
                    console.log('Loading project data for ID:', projectId);
                    
                    const res = await fetch(`/student/projects/${projectId}/edit-data`, {
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                        }
                    });
                    
                    console.log('Response status:', res.status);
                    
                    const data = await res.json();
                    console.log('Response data:', data);
                    
                    if (!res.ok) {
                        const errorMsg = data.message || `HTTP Error ${res.status}`;
                        console.error('Server error:', errorMsg);
                        alert('Gagal memuat data proyek: ' + errorMsg);
                        return;
                    }
                    
                    if (!data.success || !data.project) {
                        console.error('Invalid response format:', data);
                        alert('Format response tidak valid');
                        return;
                    }
                    
                    this.editingProject = data.project;
                    this.projectType = data.project.type;
                    
                    // Debug: Log the media data
                    console.log('Raw media data:', data.project.media);
                    
                    this.projectData = {
                        title: data.project.title || '',
                        description: data.project.description || '',
                        price: data.project.price || '',
                        status: data.project.status || 'draft',
                        categories: data.project.categories?.map(c => c.id) || [],
                        subjects: data.project.subjects?.map(s => s.id) || [],
                        teachers: data.project.teachers?.map(t => t.id) || [],
                        team_members: data.project.team_members?.filter(m => m.role !== 'leader').map(m => m.student_id) || [],
                        team_positions: {},
                        existing_images: data.project.media?.map((m, index) => ({
                            id: m.id,
                            url: m.url || (m.file_path ? '/storage/' + m.file_path : ''),
                            file_path: m.file_path,
                            alt_text: 'Project Image',
                            is_main: index === 0
                        })) || [],
                        images_to_delete: []
                    };
                    
                    // Debug: Log the processed existing_images
                    console.log('Processed existing_images:', this.projectData.existing_images);
                    
                    // Populate team positions
                    if (data.project.team_members) {
                        data.project.team_members.forEach(member => {
                            if (member.role !== 'leader') {
                                this.projectData.team_positions[member.student_id] = member.position || '';
                            }
                        });
                    }
                    
                    // Store original data for change detection
                    this.originalProjectData = JSON.parse(JSON.stringify(this.projectData));
                    this.originalTeamPositions = JSON.parse(JSON.stringify(this.projectData.team_positions));
                    
                    this.currentStep = 1;
                    this.showEditProjectModal = true;
                    console.log('Modal opened successfully');
                } catch (e) {
                    console.error('Error loading project:', e);
                    alert('Gagal memuat data proyek: ' + e.message);
                }
            },

            // Update project
            async updateProject() {
                if (!this.canCreateProject()) {
                    alert('Mohon lengkapi semua data yang diperlukan');
                    return;
                }
                
                const formData = new FormData();
                formData.append('_method', 'PUT');
                formData.append('title', this.projectData.title);
                formData.append('description', this.projectData.description);
                formData.append('price', this.projectData.price || '');
                formData.append('status', this.projectData.status);
                
                this.projectData.categories.forEach(id => formData.append('categories[]', id));
                this.projectData.subjects.forEach(id => formData.append('subjects[]', id));
                this.projectData.teachers.forEach(id => formData.append('teachers[]', id));
                
                if (this.projectType === 'team') {
                    this.projectData.team_members.forEach((id, idx) => {
                        formData.append('team_members[]', id);
                        formData.append(`team_positions[${idx}]`, this.projectData.team_positions[id] || '');
                    });
                }
                
                // Add images to delete
                if (this.projectData.images_to_delete && this.projectData.images_to_delete.length > 0) {
                    this.projectData.images_to_delete.forEach(id => {
                        formData.append('images_to_delete[]', id);
                    });
                }
                
                // Add new media files
                if (this.selectedFiles && this.selectedFiles.length > 0) {
                    this.selectedFiles.forEach((file, idx) => {
                        formData.append(`new_media[${idx}]`, file);
                    });
                }
                
                try {
                    const res = await fetch(`/student/projects/${this.editingProject.id}`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                        },
                        body: formData
                    });
                    
                    if (!res.ok) throw new Error('Update failed');
                    
                    alert('Proyek berhasil diperbarui!');
                    this.showEditProjectModal = false;
                    this.resetProjectModal();
                    window.location.reload();
                } catch (e) {
                    console.error(e);
                    alert('Gagal memperbarui proyek');
                }
            },

            // Delete project with SweetAlert confirmation
            async deleteProject(projectId, projectTitle) {
                const result = await Swal.fire({
                    title: 'Hapus Proyek?',
                    html: `Apakah Anda yakin ingin menghapus proyek <strong>${projectTitle}</strong>?<br><small class='text-gray-500'>Tindakan ini tidak dapat dibatalkan.</small>`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#b01116',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal',
                    reverseButtons: true
                });

                if (!result.isConfirmed) return;

                try {
                    const res = await fetch(`/student/projects/${projectId}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                        },
                        body: JSON.stringify({ _method: 'DELETE' })
                    });

                    const data = await res.json();

                    if (!res.ok || !data.success) {
                        throw new Error(data.message || 'Gagal menghapus proyek');
                    }

                    await Swal.fire({
                        title: 'Berhasil!',
                        text: data.message || 'Proyek berhasil dihapus',
                        icon: 'success',
                        confirmButtonColor: '#b01116',
                        timer: 2000
                    });

                    window.location.reload();
                } catch (e) {
                    console.error('Error deleting project:', e);
                    await Swal.fire({
                        title: 'Gagal!',
                        text: e.message || 'Terjadi kesalahan saat menghapus proyek',
                        icon: 'error',
                        confirmButtonColor: '#b01116'
                    });
                }
            },

            // Toggles
            toggleCategory(id) {
                const i = this.projectData.categories.indexOf(id);
                i > -1 ? this.projectData.categories.splice(i, 1) : this.projectData.categories.push(id);
            },
            toggleSubject(id) {
                const i = this.projectData.subjects.indexOf(id);
                i > -1 ? this.projectData.subjects.splice(i, 1) : this.projectData.subjects.push(id);
            },
            toggleTeacher(id) {
                const i = this.projectData.teachers.indexOf(id);
                i > -1 ? this.projectData.teachers.splice(i, 1) : this.projectData.teachers.push(id);
            },
            toggleTeamMember(id) {
                const i = this.projectData.team_members.indexOf(id);
                if (i > -1) {
                    this.projectData.team_members.splice(i, 1);
                    delete this.projectData.team_positions[id];
                } else {
                    this.projectData.team_members.push(id);
                    this.projectData.team_positions[id] = '';
                }
            },
            toggleExpertise(id) {
                const i = this.selectedExpertises.indexOf(id);
                i > -1 ? this.selectedExpertises.splice(i, 1) : this.selectedExpertises.push(id);
            },

            // Education
            addEducation() {
                this.education.push({
                    id: 'new_' + Date.now(),
                    institution_name: this.newEducation.institution_name,
                    degree: this.newEducation.degree,
                    field_of_study: this.newEducation.field_of_study,
                    start_date: this.newEducation.start_date,
                    end_date: this.newEducation.end_date,
                    is_current: this.newEducation.is_current,
                    description: this.newEducation.description
                });
                this.newEducation = {
                    institution_name: '',
                    degree: '',
                    field_of_study: '',
                    start_date: '',
                    end_date: '',
                    is_current: false,
                    description: ''
                };
            },
            removeEducation(i) {
                this.education.splice(i, 1);
            },

            // Image management functions
            setAsMainImage(index, type = 'existing') {
                if (type === 'existing' && this.projectData.existing_images && this.projectData.existing_images.length > 0) {
                    // Move the selected image to the first position
                    const selectedImage = this.projectData.existing_images.splice(index, 1)[0];
                    this.projectData.existing_images.unshift(selectedImage);
                }
            },

            markImageForDeletion(index) {
                if (!this.projectData.images_to_delete) {
                    this.projectData.images_to_delete = [];
                }
                
                const imageId = this.projectData.existing_images[index].id || index;
                
                if (this.projectData.images_to_delete.includes(imageId)) {
                    // Remove from deletion list
                    const deleteIndex = this.projectData.images_to_delete.indexOf(imageId);
                    this.projectData.images_to_delete.splice(deleteIndex, 1);
                } else {
                    // Add to deletion list
                    this.projectData.images_to_delete.push(imageId);
                }
            },

            getTotalImagesCount() {
                const existingCount = this.getExistingImagesCount();
                const newCount = this.previews ? this.previews.length : 0;
                return existingCount + newCount;
            },

            getExistingImagesCount() {
                if (!this.projectData.existing_images) return 0;
                const deletedCount = this.getDeletedImagesCount();
                return this.projectData.existing_images.length - deletedCount;
            },

            getDeletedImagesCount() {
                return this.projectData.images_to_delete ? this.projectData.images_to_delete.length : 0;
            },

            // Change detection functions
            hasChanged(field) {
                if (!this.originalProjectData) return false;
                
                if (Array.isArray(this.projectData[field])) {
                    // For arrays, check if they have the same elements
                    const original = this.originalProjectData[field] || [];
                    const current = this.projectData[field] || [];
                    
                    if (original.length !== current.length) return true;
                    return original.some(item => !current.includes(item)) || current.some(item => !original.includes(item));
                }
                
                return this.projectData[field] !== this.originalProjectData[field];
            },

            hasImagesChanged() {
                // Check if there are new images or deleted images
                return (this.previews && this.previews.length > 0) || 
                       (this.projectData.images_to_delete && this.projectData.images_to_delete.length > 0);
            },

            getOriginalValue(field) {
                return this.originalProjectData ? this.originalProjectData[field] : null;
            },

            // Navigation functions
            nextStep() {
                if (this.canProceedToNext() && this.currentStep < this.totalSteps) {
                    this.currentStep++;
                }
            },

            prevStep() {
                if (this.currentStep > 1) {
                    this.currentStep--;
                }
            },

            canProceedToNext() {
                if (this.currentStep === 1) {
                    return this.projectData.title.trim() !== '';
                } else if (this.currentStep === 2) {
                    return this.projectData.categories.length > 0;
                }
                return true;
            },

            canCreateProject() {
                const hasTitle = this.projectData.title.trim() !== '';
                const hasCategories = this.projectData.categories.length > 0;
                const hasMinImages = this.getTotalImagesCount() >= 1;
                const hasTeamMembers = this.projectType === 'individual' || this.projectData.team_members.length > 0;
                
                return hasTitle && hasCategories && hasMinImages && hasTeamMembers;
            },

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
                    team_positions: {},
                    existing_images: [],
                    images_to_delete: []
                };
                this.originalProjectData = null;
                this.originalTeamPositions = null;
                this.editingProject = null;
                
                // Clear search filters
                this.searchCategory = '';
                this.searchSubject = '';
                this.searchTeacher = '';
                this.searchStudent = '';
                
                // Hide add forms
                this.showAddCategory = false;
                this.showAddSubject = false;
                this.showAddTeacher = false;
                
                // Clear new data forms
                this.newCategory = { name: '', description: '' };
                this.newSubject = { name: '', code: '', description: '' };
                this.newTeacher = { name: '', nip: '', email: '', phone_number: '', institution: '' };
            }
        }"
         x-effect="document.documentElement.classList.toggle('overflow-hidden', showEditModal || showIndividualProjectModal || showTeamProjectModal || showEditProjectModal)">
        {{-- <!-- Left Column (2 columns width) --> --}}
        <div class="flex-1 lg:w-2/3 lg:order-1 order-2">
            <!-- Quick Actions Bar -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-6">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                    <div class="mb-4 sm:mb-0">
                        <h3 class="text-lg font-semibold text-gray-800 mb-1">Aksi Cepat</h3>
                        <p class="text-sm text-gray-600">Buat proyek baru atau inisiasi proyek tim</p>
                    </div>
                    <div class="flex flex-col sm:flex-row gap-3">
                        <button @click="showIndividualProjectModal = true; projectType = 'individual'; resetProjectModal()" 
                                class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-[#b01116] hover:bg-[#8d0d11] text-white rounded-lg font-medium transition-all shadow-md hover:shadow-lg transform hover:-translate-y-0.5">
                            <i class="ri-user-line"></i>
                            <span>Proyek Individual</span>
                        </button>
                        <button @click="showTeamProjectModal = true; projectType = 'team'; resetProjectModal()" 
                                class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-gradient-to-r from-[#b01116] to-[#8d0d11] hover:from-[#8d0d11] hover:to-[#b01116] text-white rounded-lg font-medium transition-all shadow-md hover:shadow-lg transform hover:-translate-y-0.5">
                            <i class="ri-team-line"></i>
                            <span>Proyek Tim</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Navigation Tabs -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-8" x-data="{ activeTab: 'all' }">
                <!-- Tab Headers -->
                <div class="flex border-b border-gray-200 overflow-x-auto">
                    <button @click="activeTab = 'all'" 
                            :class="activeTab === 'all' ? 'border-[#b01116] text-[#b01116]' : 'border-transparent text-gray-500 hover:text-gray-700'"
                            class="flex-1 min-w-max px-6 py-4 text-sm font-semibold border-b-2 transition-colors whitespace-nowrap">
                        <i class="ri-folder-3-line mr-2"></i>Semua Proyek
                    </button>
                    <button @click="activeTab = 'team'" 
                            :class="activeTab === 'team' ? 'border-[#b01116] text-[#b01116]' : 'border-transparent text-gray-500 hover:text-gray-700'"
                            class="flex-1 min-w-max px-6 py-4 text-sm font-semibold border-b-2 transition-colors whitespace-nowrap">
                        <i class="ri-team-line mr-2"></i>Proyek Tim
                    </button>
                    <button @click="activeTab = 'personal'" 
                            :class="activeTab === 'personal' ? 'border-[#b01116] text-[#b01116]' : 'border-transparent text-gray-500 hover:text-gray-700'"
                            class="flex-1 min-w-max px-6 py-4 text-sm font-semibold border-b-2 transition-colors whitespace-nowrap">
                        <i class="ri-user-line mr-2"></i>Proyek Pribadi
                    </button>
                    <button @click="activeTab = 'about'" 
                            :class="activeTab === 'about' ? 'border-[#b01116] text-[#b01116]' : 'border-transparent text-gray-500 hover:text-gray-700'"
                            class="flex-1 min-w-max px-6 py-4 text-sm font-semibold border-b-2 transition-colors whitespace-nowrap">
                        <i class="ri-information-line mr-2"></i>Tentang Saya
                    </button>
                </div>

                <!-- Tab Content -->
                <div class="p-6">
                    <!-- All Projects Tab -->
                    <div x-show="activeTab === 'all'" x-transition>
                        <div class="mb-6">
                            <h2 class="text-xl font-bold text-gray-800 mb-2">Semua Proyek</h2>
                            <p class="text-gray-600">Koleksi lengkap proyek yang telah dikerjakan, baik secara individu maupun tim.</p>
                        </div>

                        <!-- Projects Grid -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                            @php
                                $allProjects = collect();
                                $existingProjectIds = [];

                                // Add personal projects
                                if(auth()->user()->student && auth()->user()->student->projects) {
                                    foreach(auth()->user()->student->projects as $project) {
                                        $project->project_type = 'individual';
                                        $allProjects->push($project);
                                        $existingProjectIds[] = $project->id;
                                    }
                                }
                                // Add team projects, skipping if already present
                                if(auth()->user()->student && auth()->user()->student->memberProjects) {
                                    foreach(auth()->user()->student->memberProjects as $project) {
                                        if (!in_array($project->id, $existingProjectIds)) {
                                            $project->project_type = 'team';
                                            $allProjects->push($project);
                                            $existingProjectIds[] = $project->id;
                                        }
                                    }
                                }
                                $allProjects = $allProjects->sortByDesc('created_at');
                            @endphp

                            @if($allProjects->count() > 0)
                                @foreach($allProjects as $project)
                                    <div class="bg-gray-50 rounded-lg border border-gray-200 overflow-hidden hover:shadow-md transition-shadow">
                                        <div class="aspect-video bg-gradient-to-br from-[#b01116] to-[#8d0d11] relative overflow-hidden">
                                            @if($project->media->where('type', 'image')->first())
                                                <img src="{{ $project->media->where('type', 'image')->first()->url }}" 
                                                     alt="{{ $project->title }}" 
                                                     class="w-full h-full object-cover">
                                            @endif
                                            @if($project->categories && $project->categories->first())
                                                <div class="absolute top-3 left-3 z-10">
                                                    <span class="inline-block px-2 py-1 text-xs font-medium bg-pink-100 hover:bg-pink-100 text-[#b01116] border border-pink-200 rounded-full">
                                                        {{ $project->categories->first()->name }}
                                                    </span>
                                                </div>  
                                            @endif
                                            <div class="absolute top-3 right-3 z-10">
                                                <span class="inline-block px-2 py-1 text-xs font-medium {{ $project->type === 'individual' ? 'bg-[#b01116]' : 'bg-[#8d0d11]' }} text-white rounded-full">
                                                    {{ $project->type === 'individual' ? 'Pribadi' : 'Tim' }}
                                                </span>
                                            </div>
                                            @if(!$project->media->where('type', 'image')->first())
                                            <div class="absolute inset-0 flex items-center justify-center">
                                                <div class="text-center text-white">
                                                    <i class="ri-code-box-line text-3xl mb-2"></i>
                                                    <p class="text-sm font-medium">{{ $project->type === 'team' ? 'Tim Project' : 'Individual Project' }}</p>
                                                </div>
                                            </div>
                                            @endif
                                        </div>
                                        <div class="p-4">
                                            <div class="flex items-center justify-between mb-1">
                                                <h3 class="font-semibold text-gray-800">{{ $project->title }}</h3>
                                                <span class="px-2 py-1 text-xs {{ $project->status === 'published' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }} rounded-full">
                                                    {{ ucfirst($project->status) }}
                                                </span>
                                            </div>
                                            <p class="text-xs text-gray-500 mb-2">{{ $project->created_at->format('d F Y') }}</p>
                                            <p class="text-sm text-gray-600 mb-3 line-clamp-2">{{ Str::limit($project->description, 100) }}</p>
                                            <div class="flex items-center justify-between text-xs text-gray-500 mb-3">
                                                <div class="flex items-center gap-1">
                                                    <i class="ri-eye-line"></i>
                                                    <span>{{ number_format($project->view_count) }}</span>
                                                </div>
                                                <a href="{{ route('projects.show', $project->slug) }}" class="text-[#b01116] hover:text-[#8d0d11] font-medium">Lihat Detail</a>
                                            </div>
                                            <div class="mt-3 pt-3 border-t border-gray-200">
                                                <div class="flex flex-wrap items-center gap-2">
                                                    @can('update', $project)
                                                        <button @click="loadProjectForEdit({{ $project->id }})" class="flex-1 text-xs text-center px-3 py-2 border border-gray-300 text-gray-600 hover:bg-gray-50 rounded-lg transition-colors">
                                                            <i class="ri-edit-line mr-1"></i>Edit
                                                        </button>
                                                    @endcan
                                                    @can('delete', $project)
                                                        <button @click="deleteProject({{ $project->id }}, '{{ $project->title }}')" class="flex-1 min-w-[110px] text-xs text-center px-3 py-2 border border-red-300 text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                                                            <i class="ri-delete-bin-line mr-1"></i>Hapus
                                                        </button>
                                                    @endcan
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="col-span-full text-center py-12">
                                    <div class="w-20 h-20 mx-auto mb-4 bg-gray-100 rounded-full flex items-center justify-center">
                                        <i class="ri-folder-line text-3xl text-gray-400"></i>
                                    </div>
                                    <h3 class="text-lg font-medium text-gray-800 mb-2">Belum ada proyek</h3>
                                    <p class="text-gray-600 mb-6">Mulai buat proyek pertama Anda atau bergabung dengan tim proyek lainnya.</p>
                                    <div class="flex flex-col sm:flex-row gap-3 justify-center items-center">
                                        <button @click="showIndividualProjectModal = true; projectType = 'individual'; resetProjectModal()" class="self-center inline-flex items-center justify-center gap-2 px-6 py-3 bg-[#b01116] hover:bg-[#8d0d11] text-white rounded-lg font-medium transition-colors">
                                            <i class="ri-add-line"></i>
                                            Buat Proyek Baru
                                        </button>
                                        {{-- flex gap-2 rounded-md font-medium bg-pink-50 hover:bg-pink-100 text-[#b01116] border border-pink-200 px-3 py-1 transition-colors ease-in-out duration-300 --}}
                                        <button @click="showTeamProjectModal = true; projectType = 'team'; resetProjectModal()" class="self-center inline-flex items-center justify-center gap-2 px-6 py-3 bg-pink-50 hover:bg-pink-100 text-[#b01116] border border-pink-200 rounded-lg font-medium transition-colors">
                                            <i class="ri-add-line"></i>
                                            Inisiasi Proyek Tim
                                        </button>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>                    <!-- Team Projects Tab -->
                    <div x-show="activeTab === 'team'" x-transition>
                        <div class="mb-6">
                            <h2 class="text-xl font-bold text-gray-800 mb-2">Proyek Tim</h2>
                            <p class="text-gray-600">Proyek yang dikerjakan bersama tim dengan kontribusi berbagai keahlian.</p>
                        </div>

                        <!-- Projects Grid -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                            @if(auth()->user()->student && auth()->user()->student->memberProjects->count() > 0)
                                @foreach(auth()->user()->student->memberProjects as $project)
                                    @php
                                        $membership = auth()->user()->student->projectMemberships->where('project_id', $project->id)->first();
                                    @endphp
                                    <div class="bg-gray-50 rounded-lg border border-gray-200 overflow-hidden hover:shadow-md transition-shadow">
                                        <div class="aspect-video bg-gradient-to-br from-[#b01116] to-[#8d0d11] relative overflow-hidden">
                                            @if($project->media->where('type', 'image')->first())
                                                <img src="{{ $project->media->where('type', 'image')->first()->url }}" 
                                                     alt="{{ $project->title }}" 
                                                     class="w-full h-full object-cover">
                                            @endif
                                            @if($project->categories && $project->categories->first())
                                                <div class="absolute top-3 left-3 z-10">
                                                    <span class="inline-block px-2 py-1 text-xs font-medium bg-pink-100 hover:bg-pink-100 text-[#b01116] border border-pink-200 rounded-full">
                                                        {{ $project->categories->first()->name }}
                                                    </span>
                                                </div>
                                            @endif
                                            <div class="absolute top-3 right-3 z-10">
                                                <span class="inline-block px-2 py-1 text-xs font-medium bg-[#8d0d11] text-white rounded-full">
                                                    Tim
                                                </span>
                                            </div>
                                            @if(!$project->media->where('type', 'image')->first())
                                            <div class="absolute inset-0 flex items-center justify-center">
                                                <div class="text-center text-white">
                                                    <i class="ri-team-line text-3xl mb-2"></i>
                                                    <p class="text-sm font-medium">Team Project</p>
                                                </div>
                                            </div>
                                            @endif
                                        </div>
                                        <div class="p-4">
                                            <div class="flex items-center justify-between mb-1">
                                                <h3 class="font-semibold text-gray-800">{{ $project->title }}</h3>
                                                <span class="px-2 py-1 text-xs {{ $project->status === 'published' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }} rounded-full">
                                                    {{ ucfirst($project->status) }}
                                                </span>
                                            </div>
                                            <p class="text-xs text-gray-500 mb-2">
                                                {{ $project->created_at->format('d F Y') }}
                                            </p>
                                            <p class="text-xs text-gray-500 mb-2">
                                                As a <span class="text-[#b01116] font-semibold">{{ ucfirst($membership->role) }}</span>
                                            </p>
                                            <p class="text-sm text-gray-600 mb-3 line-clamp-2">{{ Str::limit($project->description, 100) }}</p>
                                            <div class="flex items-center justify-between text-xs text-gray-500 mb-3">
                                                <div class="flex items-center gap-1">
                                                    <i class="ri-eye-line"></i>
                                                    <span>{{ number_format($project->view_count) }}</span>
                                                </div>
                                                <a href="{{ route('projects.show', $project->slug) }}" class="text-[#b01116] hover:text-[#8d0d11] font-medium">Lihat Detail</a>
                                            </div>
                                            <div class="mt-3 pt-3 border-t border-gray-200">
                                                <div class="flex items-center gap-2">
                                                    @can('update', $project)
                                                        <button @click="loadProjectForEdit({{ $project->id }})" class="flex-1 text-xs text-center px-3 py-2 border border-gray-300 text-gray-600 hover:bg-gray-50 rounded-lg transition-colors">
                                                            <i class="ri-edit-line mr-1"></i>Edit
                                                        </button>
                                                    @endcan
                                                    @can('delete', $project)
                                                        <button @click="deleteProject({{ $project->id }}, '{{ $project->title }}')" class="flex-1 text-xs text-center px-3 py-2 border border-red-300 text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                                                            <i class="ri-delete-bin-line mr-1"></i>Hapus
                                                        </button>
                                                    @endcan
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="col-span-full text-center py-12">
                                    <div class="w-20 h-20 mx-auto mb-4 bg-gray-100 rounded-full flex items-center justify-center">
                                        <i class="ri-team-line text-3xl text-gray-400"></i>
                                    </div>
                                    <h3 class="text-lg font-medium text-gray-800 mb-2">Belum bergabung dengan proyek tim</h3>
                                    <p class="text-gray-600 mb-6">Cari proyek tim yang sesuai dengan keahlian Anda dan bergabunglah untuk berkolaborasi.</p>
                                    <button @click="showTeamProjectModal = true; projectType = 'team'; resetProjectModal()" class="inline-flex items-center text-center justify-center gap-2 px-6 py-3 bg-[#b01116] hover:bg-[#8d0d11] text-white rounded-lg font-medium transition-colors">
                                        <i class="ri-add-line"></i>
                                        Inisiasi Proyek Tim
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>                    <!-- Personal Projects Tab -->
                    <!-- Personal Projects Tab -->
                    <div x-show="activeTab === 'personal'" x-transition>
                        <div class="mb-6">
                            <h2 class="text-xl font-bold text-gray-800 mb-2">Proyek Pribadi</h2>
                            <p class="text-gray-600">Proyek yang dikerjakan secara mandiri untuk mengasah skill dan kreativitas.</p>
                        </div>

                        <!-- Projects Grid -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                            @if(auth()->user()->student && auth()->user()->student->projects->where('type', 'individual')->count() > 0)
                                @foreach(auth()->user()->student->projects->where('type', 'individual') as $project)
                                    <div class="bg-gray-50 rounded-lg border border-gray-200 overflow-hidden hover:shadow-md transition-shadow">
                                        <div class="aspect-video bg-gradient-to-br from-[#b01116] to-[#8d0d11] relative overflow-hidden">
                                            @if($project->media->where('type', 'image')->first())
                                                <img src="{{ $project->media->where('type', 'image')->first()->url }}" 
                                                     alt="{{ $project->title }}" 
                                                     class="w-full h-full object-cover">
                                            @endif
                                            @if($project->categories && $project->categories->first())
                                                <div class="absolute top-3 left-3 z-10">
                                                    <span class="inline-block px-2 py-1 text-xs font-medium bg-pink-100 hover:bg-pink-100 text-[#b01116] border border-pink-200 rounded-full">
                                                        {{ $project->categories->first()->name }}
                                                    </span>
                                                </div>
                                            @endif
                                            <div class="absolute top-3 right-3 z-10">
                                                <span class="inline-block px-2 py-1 text-xs font-medium bg-[#b01116] text-white rounded-full">
                                                    {{ ucfirst($project->type) }}
                                                </span>
                                            </div>
                                            @if(!$project->media->where('type', 'image')->first())
                                            <div class="absolute inset-0 flex items-center justify-center">
                                                <div class="text-center text-white">
                                                    <i class="ri-user-line text-3xl mb-2"></i>
                                                    <p class="text-sm font-medium">Personal Project</p>
                                                </div>
                                            </div>
                                            @endif
                                        </div>
                                        <div class="p-4">
                                            <div class="flex items-center justify-between mb-1">
                                                <h3 class="font-semibold text-gray-800">{{ $project->title }}</h3>
                                                <span class="px-2 py-1 text-xs {{ $project->status === 'published' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }} rounded-full">
                                                    {{ ucfirst($project->status) }}
                                                </span>
                                            </div>
                                            <p class="text-xs text-gray-500 mb-2">{{ $project->created_at->format('d F Y') }}</p>
                                            <p class="text-sm text-gray-600 mb-3 line-clamp-2">{{ Str::limit($project->description, 100) }}</p>
                                            <div class="flex items-center justify-between text-xs text-gray-500 mb-3">
                                                <div class="flex items-center gap-1">
                                                    <i class="ri-eye-line"></i>
                                                    <span>{{ number_format($project->view_count) }}</span>
                                                </div>
                                                <a href="{{ route('projects.show', $project->slug) }}" class="text-[#b01116] hover:text-[#8d0d11] font-medium">Lihat Detail</a>
                                            </div>
                                            <div class="mt-3 pt-3 border-t border-gray-200">
                                                <div class="flex items-center gap-2">
                                                    @can('update', $project)
                                                        <button @click="loadProjectForEdit({{ $project->id }})" class="flex-1 text-xs text-center px-3 py-2 border border-gray-300 text-gray-600 hover:bg-gray-50 rounded-lg transition-colors">
                                                            <i class="ri-edit-line mr-1"></i>Edit
                                                        </button>
                                                    @endcan
                                                    @can('delete', $project)
                                                        <button @click="deleteProject({{ $project->id }}, '{{ $project->title }}')" class="flex-1 text-xs text-center px-3 py-2 border border-red-300 text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                                                            <i class="ri-delete-bin-line mr-1"></i>Hapus
                                                        </button>
                                                    @endcan
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="col-span-full text-center py-12">
                                    <div class="w-20 h-20 mx-auto mb-4 bg-gray-100 rounded-full flex items-center justify-center">
                                        <i class="ri-user-line text-3xl text-gray-400"></i>
                                    </div>
                                    <h3 class="text-lg font-medium text-gray-800 mb-2">Belum ada proyek pribadi</h3>
                                    <p class="text-gray-600 mb-6">Buat proyek pribadi pertama Anda untuk menampilkan keahlian dan kreativitas.</p>
                                    <button @click="showIndividualProjectModal = true; projectType = 'individual'; resetProjectModal()" class="inline-flex items-center text-center justify-center gap-2 px-6 py-3 bg-[#b01116] hover:bg-[#8d0d11] text-white rounded-lg font-medium transition-colors">
                                        <i class="ri-add-line"></i>
                                        Buat Proyek Baru
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>                    <!-- About Tab -->
                    <div x-show="activeTab === 'about'" x-transition>
                        <div class="prose prose-gray max-w-none">
                            <h2 class="text-2xl font-bold text-gray-800 mb-4">Tentang Saya</h2>
                            <p class="text-gray-600 leading-relaxed mb-4">
                                {{ auth()->user()->about ?? 'Belum ada deskripsi tentang diri. Klik tombol Edit Profil untuk menambahkan informasi tentang Anda.' }}
                            </p>
                            
                            <h3 class="text-xl font-bold text-gray-800 mb-3 mt-6">Keahlian</h3>
                            <div class="flex flex-wrap gap-2 mb-6">
                                @if(auth()->user()->student && auth()->user()->student->expertises->count() > 0)
                                    @foreach(auth()->user()->student->expertises as $expertise)
                                        <span class="px-3 py-2 bg-red-100 text-gray-700 text-sm font-medium rounded-full">
                                            {{ $expertise->name }}
                                        </span>
                                    @endforeach
                                @else
                                    <p class="text-gray-500 text-sm">Belum ada keahlian yang ditambahkan. Klik tombol Edit Profil untuk menambahkan keahlian Anda.</p>
                                @endif
                            </div>

                            <h3 class="text-xl font-bold text-gray-800 mb-3 mt-6">Pendidikan</h3>
                            <div class="space-y-4">
                                @if(auth()->user()->student && auth()->user()->student->educationInfo->count() > 0)
                                    @foreach(auth()->user()->student->educationInfo->sortByDesc('is_current') as $education)
                                        <div class="border-l-4 border-[#b01116] pl-4">
                                            <h4 class="font-semibold text-gray-800">
                                                {{ $education->degree ? $education->degree . ' ' : '' }}{{ $education->field_of_study }}
                                            </h4>
                                            <p class="text-sm text-gray-600">{{ $education->institution_name }}</p>
                                            <p class="text-xs text-gray-500">{{ $education->period }}</p>
                                            @if($education->description)
                                                <p class="text-sm text-gray-600 mt-1">{{ $education->description }}</p>
                                            @endif
                                        </div>
                                    @endforeach
                                @else
                                    <p class="text-gray-500 text-sm">Belum ada informasi pendidikan. Klik tombol Edit Profil untuk menambahkan riwayat pendidikan Anda.</p>
                                @endif
                            </div>

                            <h3 class="text-xl font-bold text-gray-800 mb-3 mt-6">Statistik</h3>
                            <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                                <div class="bg-gray-50 rounded-lg p-4 text-center border border-gray-200">
                                    <div class="text-2xl font-bold text-[#b01116] mb-1">{{ auth()->user()->student->projects->count() ?? 0 }}</div>
                                    <div class="text-sm text-gray-600">Proyek Pribadi</div>
                                </div>
                                <div class="bg-gray-50 rounded-lg p-4 text-center border border-gray-200">
                                    <div class="text-2xl font-bold text-[#b01116] mb-1">{{ auth()->user()->student->projectMemberships->count() ?? 0 }}</div>
                                    <div class="text-sm text-gray-600">Proyek Tim</div>
                                </div>
                                <div class="bg-gray-50 rounded-lg p-4 text-center border border-gray-200">
                                    <div class="text-2xl font-bold text-[#b01116] mb-1">{{ auth()->user()->student->expertises->count() ?? 0 }}</div>
                                    <div class="text-sm text-gray-600">Keahlian</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column (Fixed Sidebar) -->
        <div class="lg:w-1/3 lg:order-2 order-1">
            <div class="lg:sticky lg:top-24">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <!-- Profile Picture -->
                    <div class="flex justify-center mb-6">
                        <div class="w-32 h-32 rounded-full overflow-hidden border-4 border-gray-200">
                            @if(auth()->user()->avatar)
                                <img src="{{ auth()->user()->avatar_url }}" alt="{{ auth()->user()->username }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full bg-gradient-to-br from-[#b01116] to-pink-600 flex items-center justify-center text-white text-4xl font-bold">
                                    {{ strtoupper(substr(auth()->user()->username, 0, 1)) }}
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Student Name -->
                    <h1 class="text-2xl font-bold text-gray-800 text-center mb-2">{{ auth()->user()->full_name ?? auth()->user()->username }}</h1>

                    <!-- Username -->
                    <p class="text-sm text-gray-500 text-center mb-4">{{ "@" . auth()->user()->username }}</p>
                    
                    <!-- Student ID -->
                    <p class="text-sm text-gray-500 text-center mb-4">NIM: {{ auth()->user()->student->student_id ?? 'Belum diisi' }}</p>

                    <!-- About (100 char limit) -->
                    <div class="mb-6 pb-6 border-b border-gray-200">
                        <p class="text-sm text-gray-600 text-center leading-relaxed">
                            {{ auth()->user()->short_about ?? 'Belum ada deskripsi singkat. Klik tombol Edit Profil untuk menambahkan.' }}
                        </p>
                    </div>

                    <!-- Edit Profile Button -->
                    <button @click="showEditModal = true; resetModal()" class="w-full bg-[#b01116] hover:bg-[#8d0d11] text-white font-semibold py-3 px-4 rounded-lg transition-colors duration-300 flex items-center justify-center gap-2 mb-6">
                        <i class="ri-edit-line"></i>
                        Edit Profil
                    </button>

                    <!-- Contact Information -->
                    <div class="space-y-3">
                        <h3 class="text-sm font-bold text-gray-800 uppercase mb-4">Kontak</h3>
                        
                        <!-- Email -->
                        <a href="mailto:{{ auth()->user()->email }}" class="flex items-center gap-3 text-gray-600 hover:text-[#b01116] transition-colors group">
                            <div class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center group-hover:bg-[#b01116]/10 transition-colors">
                                <i class="ri-mail-line text-lg"></i>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">Email</p>
                                <p class="text-sm font-medium">{{ auth()->user()->email }}</p>
                            </div>
                        </a>

                        @if(auth()->user()->phone_number)
                        <!-- Phone -->
                        <a href="tel:{{ auth()->user()->phone_number }}" class="flex items-center gap-3 text-gray-600 hover:text-[#b01116] transition-colors group">
                            <div class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center group-hover:bg-[#b01116]/10 transition-colors">
                                <i class="ri-phone-line text-lg"></i>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">Telepon</p>
                                <p class="text-sm font-medium">{{ auth()->user()->phone_number }}</p>
                            </div>
                        </a>
                        @endif
                    </div>

                    <!-- Multi-Step Edit Profile Modal (teleported to <body>) -->
                    <template x-teleport="body">
                        <div x-show="showEditModal"
                             x-transition
                             @keydown.escape.window="showEditModal = false; resetModal()"
                             @click.self="showEditModal = false; resetModal()"
                             class="fixed inset-0 z-[200] bg-black/50 flex items-center justify-center p-4"
                             role="dialog" aria-modal="true" style="display: none;">
                            <div class="bg-white rounded-xl shadow-xl max-w-4xl w-full max-h-[90vh] overflow-y-auto" @click.stop>
                                
                                <!-- Modal Header with Progress -->
                                <div class="sticky top-0 bg-white border-b border-gray-200 px-6 py-4">
                                    <div class="flex items-center justify-between mb-4">
                                        <h2 class="text-xl font-bold text-gray-800">Edit Profil</h2>
                                        <button @click="showEditModal = false; resetModal()" class="text-gray-400 hover:text-gray-600">
                                            <i class="ri-close-line text-2xl"></i>
                                        </button>
                                    </div>
                                    
                                    <!-- Progress Steps -->
                                    <div class="flex items-center justify-center">
                                        <template x-for="step in totalSteps" :key="step">
                                            <div class="flex items-center">
                                                <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-medium transition-colors"
                                                     :class="step <= currentStep ? 'bg-[#b01116] text-white' : 'bg-gray-200 text-gray-500'">
                                                    <span x-text="step"></span>
                                                </div>
                                                <div class="w-16 h-1 mx-2 transition-colors" 
                                                     x-show="step < totalSteps"
                                                     :class="step < currentStep ? 'bg-[#b01116]' : 'bg-gray-200'"></div>
                                            </div>
                                        </template>
                                    </div>
                                    
                                    <div class="text-center mt-2 text-sm text-gray-600">
                                        <span x-show="currentStep === 1">Langkah 1: Informasi Dasar</span>
                                        <span x-show="currentStep === 2">Langkah 2: Keahlian</span>
                                        <span x-show="currentStep === 3">Langkah 3: Pendidikan</span>
                                    </div>
                                </div>

                                <!-- Modal Body -->
                                <form action="{{ route('student.update') }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    @method('PUT')
                                    
                                    <!-- Hidden field for expertises -->
                                    <template x-for="(expertiseId, index) in selectedExpertises" :key="expertiseId">
                                        <input type="hidden" :name="'expertises[' + index + ']'" :value="expertiseId">
                                    </template>
                                    
                                    <!-- Hidden field for education -->
                                    <template x-for="(edu, index) in education" :key="index">
                                        <div>
                                            <input type="hidden" :name="'education[' + index + '][institution_name]'" :value="edu.institution_name">
                                            <input type="hidden" :name="'education[' + index + '][degree]'" :value="edu.degree">
                                            <input type="hidden" :name="'education[' + index + '][field_of_study]'" :value="edu.field_of_study">
                                            <input type="hidden" :name="'education[' + index + '][start_date]'" :value="edu.start_date">
                                            <input type="hidden" :name="'education[' + index + '][end_date]'" :value="edu.end_date">
                                            <input type="hidden" :name="'education[' + index + '][is_current]'" :value="edu.is_current ? 1 : 0">
                                            <input type="hidden" :name="'education[' + index + '][description]'" :value="edu.description">
                                            <input type="hidden" x-show="!String(edu.id).startsWith('new_')" :name="'education[' + index + '][id]'" :value="edu.id">
                                        </div>
                                    </template>

                                    <!-- Step 1: Basic Information -->
                                    <div x-show="currentStep === 1" x-transition class="p-6">
                                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Informasi Dasar</h3>
                                        
                                        <!-- Avatar Upload -->
                                        <div class="mb-6">
                                            <label class="block text-sm font-semibold text-gray-700 mb-2">Foto Profil</label>
                                            <div class="flex items-center gap-4">
                                                <div class="w-20 h-20 rounded-full overflow-hidden border-4 border-gray-200 shrink-0 relative">
                                                    <!-- Preview Image -->
                                                    <img x-show="avatarPreview" 
                                                         :src="avatarPreview" 
                                                         alt="Avatar Preview" 
                                                         class="w-full h-full object-cover absolute inset-0">
                                                    
                                                    <!-- Current Avatar or Placeholder -->
                                                    <div x-show="!avatarPreview" class="w-full h-full absolute inset-0">
                                                        @if(auth()->user()->avatar)
                                                            <img src="{{ auth()->user()->avatar_url }}" alt="Avatar" class="w-full h-full object-cover">
                                                        @else
                                                            <div class="w-full h-full bg-gradient-to-br from-[#b01116] to-pink-600 flex items-center justify-center text-white text-2xl font-bold">
                                                                {{ strtoupper(substr(auth()->user()->username, 0, 1)) }}
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="flex-1">
                                                    <input type="file" 
                                                           name="avatar" 
                                                           accept="image/*" 
                                                           @change="handleAvatarPreview($event)"
                                                           class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-[#b01116] file:text-white hover:file:bg-[#8d0d11] cursor-pointer">
                                                    <p class="text-xs text-gray-500 mt-1">JPG, PNG atau GIF (Maks. 2MB)</p>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <!-- Full Name -->
                                            <div>
                                                <label for="full_name" class="block text-sm font-semibold text-gray-700 mb-2">Nama Lengkap *</label>
                                                <input type="text" name="full_name" id="full_name" value="{{ old('full_name', auth()->user()->full_name) }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#b01116] focus:border-transparent">
                                            </div>

                                            <!-- Student ID -->
                                            <div>
                                                <label for="student_id" class="block text-sm font-semibold text-gray-700 mb-2">NIM *</label>
                                                <input type="text" name="student_id" id="student_id" value="{{ old('student_id', auth()->user()->student->student_id) }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#b01116] focus:border-transparent">
                                            </div>

                                            <!-- Phone -->
                                            <div>
                                                <label for="phone_number" class="block text-sm font-semibold text-gray-700 mb-2">Nomor Telepon *</label>
                                                <input type="tel" name="phone_number" id="phone_number" value="{{ old('phone_number', auth()->user()->phone_number) }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#b01116] focus:border-transparent">
                                            </div>

                                            <!-- Email (readonly) -->
                                            <div>
                                                <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">Email</label>
                                                <input type="email" id="email" value="{{ auth()->user()->email }}" readonly class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50 cursor-not-allowed">
                                            </div>
                                        </div>

                                        <!-- Short About -->
                                        <div class="mt-4">
                                            <label for="short_about" class="block text-sm font-semibold text-gray-700 mb-2">Deskripsi Singkat (Maks. 500 karakter)</label>
                                            <textarea name="short_about" id="short_about" rows="3" maxlength="500" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#b01116] focus:border-transparent">{{ old('short_about', auth()->user()->short_about) }}</textarea>
                                        </div>

                                        <!-- About -->
                                        <div class="mt-4">
                                            <label for="about" class="block text-sm font-semibold text-gray-700 mb-2">Tentang Saya</label>
                                            <textarea name="about" id="about" rows="6" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#b01116] focus:border-transparent">{{ old('about', auth()->user()->about) }}</textarea>
                                        </div>
                                    </div>

                                    <!-- Step 2: Expertise Selection -->
                                    <div x-show="currentStep === 2" x-transition class="p-6">
                                        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                                            <i class="ri-lightbulb-line text-[#b01116]"></i>
                                            Pilih Keahlian Anda
                                        </h3>
                                        <p class="text-sm text-gray-600 mb-4">Pilih keahlian yang Anda kuasai. Anda dapat memilih lebih dari satu keahlian.</p>
                                        
                                        <!-- Search Box -->
                                        <div class="relative mb-4">
                                            <i class="ri-search-line absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 text-lg"></i>
                                            <input 
                                                x-model="searchExpertise" 
                                                type="text" 
                                                placeholder="Cari keahlian..." 
                                                class="w-full pl-10 pr-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-[#b01116] focus:border-[#b01116] transition-all">
                                        </div>
                                        
                                        <!-- Selection Grid -->
                                        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3 max-h-72 overflow-y-auto p-1 mb-4">
                                            <template x-for="expertise in filteredExpertises" :key="'modal-exp-'+expertise.id">
                                                <label class="flex items-center p-3 border-2 rounded-lg cursor-pointer hover:shadow-md transition-all"
                                                       :class="selectedExpertises.includes(expertise.id) ? 'border-[#b01116] bg-red-50 shadow-sm' : 'border-gray-200 hover:border-gray-300'">
                                                    <input type="checkbox" 
                                                           class="sr-only" 
                                                           :checked="selectedExpertises.includes(expertise.id)"
                                                           @change="toggleExpertise(expertise.id)">
                                                    <div class="w-5 h-5 rounded border-2 mr-3 flex items-center justify-center transition-all"
                                                         :class="selectedExpertises.includes(expertise.id) ? 'bg-[#b01116] border-[#b01116] scale-110' : 'border-gray-300'">
                                                        <i class="ri-check-line text-white text-sm font-bold" x-show="selectedExpertises.includes(expertise.id)"></i>
                                                    </div>
                                                    <span class="text-sm font-medium text-gray-700 leading-tight" x-text="expertise.name"></span>
                                                </label>
                                            </template>
                                        </div>
                                        
                                        <!-- No Results -->
                                        <div x-show="filteredExpertises.length === 0 && searchExpertise !== ''" class="text-center py-12 text-gray-500">
                                            <i class="ri-search-line text-5xl mb-3 opacity-50"></i>
                                            <p class="font-medium">Tidak ada keahlian yang sesuai</p>
                                            <p class="text-sm mt-1">Coba kata kunci lain atau tambahkan keahlian baru</p>
                                        </div>
                                        
                                        <!-- Add New Expertise Button -->
                                        <button 
                                            type="button"
                                            @click="showAddExpertise = !showAddExpertise"
                                            class="w-full mt-3 py-3 border-2 border-dashed rounded-lg text-gray-600 hover:border-[#b01116] hover:text-[#b01116] hover:bg-red-50 transition-all flex items-center justify-center gap-2 font-medium">
                                            <i class="ri-add-circle-line text-xl" :class="showAddExpertise ? 'rotate-45 transition-transform' : ''"></i>
                                            <span x-text="showAddExpertise ? 'Batal Tambah' : 'Tambah Keahlian Baru'"></span>
                                        </button>
                                        
                                        <!-- Inline Create Form -->
                                        <div x-show="showAddExpertise" x-transition x-collapse class="mt-4 p-4 bg-gradient-to-br from-red-50 to-pink-50 rounded-lg border-2 border-red-200">
                                            <h5 class="font-semibold text-gray-800 mb-3 flex items-center gap-2">
                                                <i class="ri-lightbulb-line text-blue-600"></i>
                                                Tambah Keahlian Baru
                                            </h5>
                                            <div class="space-y-3">
                                                <div>
                                                    <label class="block text-xs font-medium text-gray-700 mb-1">Nama Keahlian</label>
                                                    <input type="text" x-model="newExpertise.name" placeholder="Contoh: JavaScript, UI/UX Design, Data Analysis" 
                                                           class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                                </div>
                                                <button type="button" @click="createExpertise()" 
                                                        class="w-full bg-blue-600 text-white py-2 px-4 rounded-lg text-sm hover:bg-blue-700 transition-colors font-medium">
                                                    <i class="ri-save-line mr-2"></i>Simpan Keahlian
                                                </button>
                                            </div>
                                        </div>
                                        
                                        <!-- Selection Counter -->
                                        <div class="mt-4 p-3 rounded-lg border"
                                             :class="selectedExpertises.length === 0 ? 'bg-gradient-to-r from-gray-50 to-gray-100 border-gray-200' : 'bg-gradient-to-r from-gray-50 to-gray-100 border-gray-300'">
                                            <div class="flex items-center gap-3"
                                                 :class="selectedExpertises.length === 0 ? 'text-gray-600' : 'text-blue-700'">
                                                <i :class="selectedExpertises.length === 0 ? 'ri-information-line' : 'ri-checkbox-circle-line'" class="text-xl"></i>
                                                <div class="flex-1">
                                                    <div class="font-semibold">
                                                        <span x-show="selectedExpertises.length === 0">Belum ada keahlian yang dipilih</span>
                                                        <span x-show="selectedExpertises.length > 0" x-text="selectedExpertises.length + ' keahlian dipilih'"></span>
                                                    </div>
                                                    <div class="text-xs opacity-75" x-show="selectedExpertises.length > 0">
                                                        Keahlian membantu menunjukkan kompetensi Anda
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Step 3: Education History -->
                                    <div x-show="currentStep === 3" x-transition class="p-6">
                                        <div class="flex items-center justify-between mb-4">
                                            <h3 class="text-lg font-semibold text-gray-800">Riwayat Pendidikan</h3>
                                            <button type="button" @click="addEducation()" class="bg-[#b01116] text-white px-4 py-2 rounded-lg text-sm hover:bg-[#8d0d11] transition-colors">
                                                <i class="ri-add-line"></i> Tambah Pendidikan
                                            </button>
                                        </div>
                                        
                                        <!-- Education List -->
                                        <div class="space-y-4 mb-6">
                                            <template x-for="(edu, index) in education" :key="index">
                                                <div class="border border-gray-200 rounded-lg p-4">
                                                    <div class="flex items-center justify-between mb-3">
                                                        <h4 class="font-medium text-gray-800" x-text="edu.institution_name || 'Institusi Baru'"></h4>
                                                        <button type="button" @click="removeEducation(index)" class="text-red-500 hover:text-red-700">
                                                            <i class="ri-delete-bin-line"></i>
                                                        </button>
                                                    </div>
                                                    
                                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                                        <div>
                                                            <label class="block text-xs font-medium text-gray-700 mb-1">Nama Institusi</label>
                                                            <input type="text" x-model="edu.institution_name" placeholder="Universitas Telkom" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#b01116] focus:border-transparent">
                                                        </div>
                                                        <div>
                                                            <label class="block text-xs font-medium text-gray-700 mb-1">Jenjang</label>
                                                            <select x-model="edu.degree" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#b01116] focus:border-transparent">
                                                                <option value="">Pilih Jenjang</option>
                                                                <option value="SD">SD</option>
                                                                <option value="SMP">SMP</option>
                                                                <option value="SMA">SMA</option>
                                                                <option value="SMK">SMK</option>
                                                                <option value="D1">D1</option>
                                                                <option value="D2">D2</option>
                                                                <option value="D3">D3</option>
                                                                <option value="D4">D4</option>
                                                                <option value="S1">S1</option>
                                                                <option value="S2">S2</option>
                                                                <option value="S3">S3</option>
                                                            </select>
                                                        </div>
                                                        <div>
                                                            <label class="block text-xs font-medium text-gray-700 mb-1">Bidang Studi</label>
                                                            <input type="text" x-model="edu.field_of_study" placeholder="Teknik Informatika" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#b01116] focus:border-transparent">
                                                        </div>
                                                        <div class="flex items-center gap-3">
                                                            <div class="flex-1">
                                                                <label class="block text-xs font-medium text-gray-700 mb-1">Tahun Mulai</label>
                                                                <input type="date" x-model="edu.start_date" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#b01116] focus:border-transparent">
                                                            </div>
                                                            <div class="flex-1">
                                                                <label class="block text-xs font-medium text-gray-700 mb-1">Tahun Selesai</label>
                                                                <input type="date" x-model="edu.end_date" :disabled="edu.is_current" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#b01116] focus:border-transparent disabled:bg-gray-50">
                                                            </div>
                                                        </div>
                                                        <div class="md:col-span-2">
                                                            <label class="flex items-center gap-2 text-sm">
                                                                <input type="checkbox" x-model="edu.is_current" class="w-4 h-4 text-[#b01116] border-gray-300 rounded focus:ring-[#b01116]">
                                                                <span class="text-gray-700">Saat ini masih bersekolah/kuliah di sini</span>
                                                            </label>
                                                        </div>
                                                        <div class="md:col-span-2">
                                                            <label class="block text-xs font-medium text-gray-700 mb-1">Deskripsi (Opsional)</label>
                                                            <textarea x-model="edu.description" rows="2" placeholder="Prestasi, aktivitas, atau deskripsi lainnya" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#b01116] focus:border-transparent"></textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                            </template>
                                            
                                            <div x-show="education.length === 0" class="text-center py-8 text-gray-500">
                                                <i class="ri-graduation-cap-line text-4xl mb-2"></i>
                                                <p>Belum ada riwayat pendidikan</p>
                                                <p class="text-sm">Klik tombol "Tambah Pendidikan" untuk menambahkan</p>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Navigation Footer -->
                                    <div class="sticky bottom-0 bg-white border-t border-gray-200 px-6 py-4">
                                        <div class="flex justify-between gap-4">
                                            <button type="button" 
                                                    @click="prevStep()" 
                                                    x-show="currentStep > 1"
                                                    class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium transition-colors">
                                                <i class="ri-arrow-left-line mr-2"></i>Kembali
                                            </button>
                                            
                                            <button type="button" 
                                                    @click="showEditModal = false; resetModal()" 
                                                    class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium transition-colors">
                                                Batal
                                            </button>
                                            
                                            <button type="button" 
                                                    @click="nextStep()" 
                                                    x-show="currentStep < totalSteps"
                                                    class="px-6 py-2 bg-[#b01116] hover:bg-[#8d0d11] text-white rounded-lg font-medium transition-colors">
                                                Selanjutnya<i class="ri-arrow-right-line ml-2"></i>
                                            </button>
                                            
                                            <button type="submit" 
                                                    x-show="currentStep === totalSteps"
                                                    class="px-6 py-2 bg-[#b01116] hover:bg-[#8d0d11] text-white rounded-lg font-medium transition-colors">
                                                <i class="ri-save-line mr-2"></i>Simpan Semua Perubahan
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>
        
<template x-teleport="body">
    <div x-show="showIndividualProjectModal"
         x-transition
         @keydown.escape.window="showIndividualProjectModal = false; resetProjectModal()"
         @click.self="showIndividualProjectModal = false; resetProjectModal()"
         class="fixed inset-0 z-[200] bg-black/60 backdrop-blur-sm flex items-center justify-center p-4"
         role="dialog" aria-modal="true" style="display: none;">
        <div class="bg-white rounded-2xl shadow-2xl max-w-5xl w-full max-h-[90vh] overflow-y-auto" @click.stop>
            
            <!-- Modal Header with Enhanced Progress -->
            <div class="sticky top-0 bg-white border-b border-gray-200 px-6 py-5 z-10">
                <div class="flex items-center justify-between mb-5">
                    <h2 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
                        <i class="ri-file-add-line text-[#b01116]"></i>
                        Buat Proyek Individu
                    </h2>
                    <button @click="showIndividualProjectModal = false; resetProjectModal()" 
                            class="text-gray-400 hover:text-gray-600 hover:bg-gray-100 w-10 h-10 rounded-full flex items-center justify-center transition-all">
                        <i class="ri-close-line text-2xl"></i>
                    </button>
                </div>
                
                <!-- Enhanced Progress Steps -->
                <div class="flex items-center justify-center gap-2">
                    <template x-for="step in totalSteps" :key="step">
                        <div class="flex items-center gap-2">
                            <div class="relative w-11 h-11 rounded-full flex items-center justify-center text-sm font-bold transition-all duration-300 shadow-sm"
                                 :class="step < currentStep ? 'bg-[#8d0d11] text-white' : step === currentStep ? 'bg-[#b01116] text-white ring-4 ring-red-100 scale-110' : 'bg-gray-200 text-gray-500'">
                                <span x-show="step < currentStep">
                                    <i class="ri-check-line text-lg"></i>
                                </span>
                                <span x-show="step >= currentStep" x-text="step"></span>
                            </div>
                            <div x-show="step < totalSteps"
                                 class="w-20 h-1.5 transition-all duration-300 rounded-full" 
                                 :class="step < currentStep ? 'bg-[#8d0d11]' : 'bg-gray-200'"></div>
                        </div>
                    </template>
                </div>
                
                <div class="text-center mt-3 text-sm font-medium text-gray-700">
                    <span x-show="currentStep === 1" class="flex items-center justify-center gap-2">
                        <i class="ri-information-line"></i>
                        Langkah 1: Informasi Proyek
                    </span>
                    <span x-show="currentStep === 2" class="flex items-center justify-center gap-2">
                        <i class="ri-price-tag-3-line"></i>
                        Langkah 2: Kategori, Mata Kuliah & Pembimbing
                    </span>
                    <span x-show="currentStep === 3" class="flex items-center justify-center gap-2">
                        <i class="ri-upload-cloud-2-line"></i>
                        Langkah 3: Media & Review
                    </span>
                </div>
            </div>

            <!-- Modal Body -->
            <form action="{{ route('student.projects.store') }}" method="POST" enctype="multipart/form-data" novalidate>
                @csrf
                <input type="hidden" name="type" value="individual">
                
                <!-- Hidden fields for form data -->
                <template x-for="(categoryId, index) in projectData.categories" :key="'cat-'+categoryId">
                    <input type="hidden" :name="'categories[' + index + ']'" :value="categoryId">
                </template>
                <template x-for="(subjectId, index) in projectData.subjects" :key="'sub-'+subjectId">
                    <input type="hidden" :name="'subjects[' + index + ']'" :value="subjectId">
                </template>
                <template x-for="(teacherId, index) in projectData.teachers" :key="'teach-'+teacherId">
                    <input type="hidden" :name="'teachers[' + index + ']'" :value="teacherId">
                </template>

                <!-- Step 1: Project Information -->
                <div x-show="currentStep === 1" x-transition class="p-6 space-y-5">
                    <h3 class="text-lg font-semibold text-gray-800 flex items-center gap-2 mb-4">
                        <i class="ri-file-text-line text-[#b01116]"></i>
                        Informasi Dasar Proyek
                    </h3>
                    
                    <!-- Project Title -->
                    <div>
                        <label for="ind_title" class="block text-sm font-semibold text-gray-700 mb-2 flex items-center gap-1">
                            Judul Proyek <span class="text-[#b01116]">*</span>
                        </label>
                        <input type="text" name="title" id="ind_title" x-model="projectData.title" required 
                               :class="projectData.title.trim() === '' ? 'border-red-300 focus:ring-red-500 focus:border-red-500' : 'border-gray-300 focus:ring-[#b01116] focus:border-[#b01116]'"
                               class="w-full px-4 py-3 border-2 rounded-lg transition-all"
                               placeholder="Masukkan judul proyek yang menarik...">
                        <p x-show="projectData.title.trim() === ''" class="text-xs text-red-500 mt-1 flex items-center gap-1">
                            <i class="ri-error-warning-line"></i>
                            Judul proyek wajib diisi
                        </p>
                    </div>

                    <!-- Project Description -->
                    <div>
                        <label for="ind_description" class="block text-sm font-semibold text-gray-700 mb-2 flex items-center gap-1">
                            Deskripsi Proyek
                        </label>
                        <textarea name="description" id="ind_description" x-model="projectData.description" rows="6" 
                                  class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-[#b01116] focus:border-[#b01116] transition-all resize-none" 
                                  placeholder="Jelaskan detail proyek Anda, tujuan, fitur utama, dan hal menarik lainnya..."></textarea>
                        <p class="text-xs text-gray-500 mt-1 flex items-center gap-1">
                            <i class="ri-lightbulb-line"></i>
                            Tips: Deskripsi yang detail akan menarik lebih banyak investor
                        </p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <!-- Project Price -->
                        <div>
                            <label for="ind_price" class="block text-sm font-semibold text-gray-700 mb-2">
                                Estimasi Harga (Rp)
                            </label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500 font-medium">Rp</span>
                                <input type="number" name="price" id="ind_price" x-model="projectData.price" min="0" step="1000"
                                       class="w-full pl-11 pr-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-[#b01116] focus:border-[#b01116] transition-all"
                                       placeholder="0">
                            </div>
                        </div>

                        <!-- Project Status -->
                        <div>
                            <label for="ind_status" class="block text-sm font-semibold text-gray-700 mb-2 flex items-center gap-1">
                                Status Publikasi <span class="text-[#b01116]">*</span>
                            </label>
                            <select name="status" id="ind_status" x-model="projectData.status" required 
                                    class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-[#b01116] focus:border-[#b01116] transition-all">
                                <option value="draft">Draft</option>
                                <option value="published">Published</option>
                                <option value="archived">Archived</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Step 2: Categories, Subjects & Teachers -->
                <div x-show="currentStep === 2" x-transition class="p-6 space-y-8">
                    <!-- CATEGORIES SECTION -->
                    <div>
                        <h4 class="font-semibold text-gray-800 mb-3 flex items-center gap-2 text-lg">
                            <i class="ri-price-tag-3-line text-[#b01116]"></i>
                            Kategori Proyek <span class="text-[#b01116]">*</span>
                        </h4>
                        <p class="text-sm text-gray-600 mb-4">Pilih minimal 1 kategori yang sesuai dengan proyek Anda</p>
                        
                        <!-- Search Box -->
                        <div class="relative mb-4">
                            <i class="ri-search-line absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 text-lg"></i>
                            <input 
                                type="text" 
                                x-model="searchCategory"
                                placeholder="Cari kategori..."
                                class="w-full pl-11 pr-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-[#b01116] focus:border-transparent transition-all">
                        </div>
                        
                        <!-- Selection Grid -->
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-3 max-h-72 overflow-y-auto p-1">
                            <template x-for="category in filteredCategories" :key="'modal-cat-'+category.id">
                                <label 
                                    class="flex items-center p-3 border-2 rounded-lg cursor-pointer hover:shadow-md transition-all duration-200 group"
                                    :class="projectData.categories.includes(category.id) ? 'border-[#b01116] bg-red-50 shadow-sm' : 'border-gray-200 hover:border-gray-300'">
                                    <input 
                                        type="checkbox" 
                                        class="sr-only" 
                                        :checked="projectData.categories.includes(category.id)"
                                        @change="toggleCategory(category.id)">
                                    <div 
                                        class="w-5 h-5 rounded border-2 mr-3 flex items-center justify-center transition-all flex-shrink-0"
                                        :class="projectData.categories.includes(category.id) ? 'bg-[#b01116] border-[#b01116] scale-110' : 'border-gray-300 group-hover:border-gray-400'">
                                        <i class="ri-check-line text-white text-sm font-bold" x-show="projectData.categories.includes(category.id)"></i>
                                    </div>
                                    <span 
                                        class="text-sm font-medium transition-colors"
                                        :class="projectData.categories.includes(category.id) ? 'text-[#b01116]' : 'text-gray-700'"
                                        x-text="category.name"></span>
                                </label>
                            </template>
                        </div>
                        
                        <!-- No Results -->
                        <div x-show="filteredCategories.length === 0" class="text-center py-12 text-gray-500">
                            <i class="ri-search-line text-5xl mb-3 opacity-50"></i>
                            <p class="font-medium">Tidak ada kategori yang sesuai</p>
                            <p class="text-sm mt-1">Coba kata kunci lain atau tambahkan kategori baru</p>
                        </div>
                        
                        <!-- Add New Button -->
                        <button 
                            type="button"
                            @click="showAddCategory = !showAddCategory"
                            class="w-full mt-3 py-3 border-2 border-dashed rounded-lg text-gray-600 hover:border-[#b01116] hover:text-[#b01116] hover:bg-red-50 transition-all flex items-center justify-center gap-2 font-medium">
                            <i class="ri-add-circle-line text-xl" :class="showAddCategory ? 'rotate-45 transition-transform' : ''"></i>
                            <span x-text="showAddCategory ? 'Batal Tambah' : 'Tambah Kategori Baru'"></span>
                        </button>
                        
                        <!-- Inline Create Form -->
                        <div x-show="showAddCategory" x-transition x-collapse class="mt-4 p-4 bg-gradient-to-br from-red-50 to-pink-50 rounded-lg border-2 border-red-200">
                            <h5 class="font-semibold text-gray-800 mb-3 flex items-center gap-2">
                                <i class="ri-add-line text-[#b01116] text-xl"></i>
                                Tambah Kategori Baru
                            </h5>
                            <div class="space-y-3">
                                <input 
                                    type="text" 
                                    x-model="newCategory.name"
                                    placeholder="Nama Kategori *"
                                    class="w-full px-4 py-2.5 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-[#b01116] focus:border-transparent">
                                <textarea 
                                    x-model="newCategory.description"
                                    placeholder="Deskripsi (opsional)"
                                    rows="2"
                                    class="w-full px-4 py-2.5 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-[#b01116] focus:border-transparent resize-none"></textarea>
                                <button 
                                    type="button"
                                    @click="createCategory()"
                                    class="w-full bg-[#b01116] hover:bg-[#8d0d11] text-white py-2.5 rounded-lg font-semibold transition-colors flex items-center justify-center gap-2 shadow-md hover:shadow-lg">
                                    <i class="ri-save-line"></i>
                                    Simpan Kategori
                                </button>
                            </div>
                        </div>
                        
                        <!-- Selection Counter -->
                        <div class="mt-4 p-3 rounded-lg border"
                             :class="projectData.categories.length === 0 ? 'bg-gradient-to-r from-red-50 to-pink-50 border-red-200' : 'bg-gradient-to-r from-red-50 to-pink-50 border-red-200'">
                            <div class="flex items-center gap-3"
                                 :class="projectData.categories.length === 0 ? 'text-red-700' : 'text-gray-700'">
                                <i :class="projectData.categories.length === 0 ? 'ri-error-warning-line' : 'ri-checkbox-circle-line'" class="text-xl"></i>
                                <div class="flex-1">
                                    <div class="font-semibold">
                                        <span x-show="projectData.categories.length === 0">Pilih minimal 1 kategori untuk melanjutkan</span>
                                        <span x-show="projectData.categories.length > 0" x-text="projectData.categories.length + ' kategori dipilih'"></span>
                                    </div>
                                    <div class="text-xs opacity-75" x-show="projectData.categories.length > 0">
                                        Kategori membantu investor menemukan proyek Anda
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SUBJECTS SECTION (Similar structure) -->
                    <div>
                        <h4 class="font-semibold text-gray-800 mb-3 flex items-center gap-2 text-lg">
                            <i class="ri-book-open-line text-[#b01116]"></i>
                            Mata Kuliah (Opsional)
                        </h4>
                        <p class="text-sm text-gray-600 mb-4">Pilih mata kuliah yang berkaitan dengan proyek ini</p>
                        
                        <!-- Search Box -->
                        <div class="relative mb-4">
                            <i class="ri-search-line absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 text-lg"></i>
                            <input 
                                type="text" 
                                x-model="searchSubject"
                                placeholder="Cari mata kuliah atau kode..."
                                class="w-full pl-11 pr-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-[#b01116] focus:border-transparent transition-all">
                        </div>
                        
                        <!-- Selection Grid -->
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-3 max-h-72 overflow-y-auto p-1">
                            <template x-for="subject in filteredSubjects" :key="'modal-sub-'+subject.id">
                                <label 
                                    class="flex flex-col p-3 border-2 rounded-lg cursor-pointer hover:shadow-md transition-all duration-200 group"
                                    :class="projectData.subjects.includes(subject.id) ? 'border-[#b01116] bg-red-50 shadow-sm' : 'border-gray-200 hover:border-gray-300'">
                                    <div class="flex items-center">
                                        <input 
                                            type="checkbox" 
                                            class="sr-only" 
                                            :checked="projectData.subjects.includes(subject.id)"
                                            @change="toggleSubject(subject.id)">
                                        <div 
                                            class="w-5 h-5 rounded border-2 mr-3 flex items-center justify-center transition-all flex-shrink-0"
                                            :class="projectData.subjects.includes(subject.id) ? 'bg-[#b01116] border-[#b01116] scale-110' : 'border-gray-300 group-hover:border-gray-400'">
                                            <i class="ri-check-line text-white text-sm font-bold" x-show="projectData.subjects.includes(subject.id)"></i>
                                        </div>
                                        <span 
                                            class="text-sm font-medium transition-colors flex-1"
                                            :class="projectData.subjects.includes(subject.id) ? 'text-[#b01116]' : 'text-gray-700'"
                                            x-text="subject.name"></span>
                                    </div>
                                    <template x-if="subject.code">
                                        <span class="text-xs text-gray-500 ml-8 mt-1" x-text="'Kode: ' + subject.code"></span>
                                    </template>
                                </label>
                            </template>
                        </div>
                        
                        <div x-show="filteredSubjects.length === 0" class="text-center py-12 text-gray-500">
                            <i class="ri-book-line text-5xl mb-3 opacity-50"></i>
                            <p class="font-medium">Tidak ada mata kuliah yang sesuai</p>
                        </div>
                        
                        <!-- Add New Button & Form (Similar to categories) -->
                        <button 
                            type="button"
                            @click="showAddSubject = !showAddSubject"
                            class="w-full mt-3 py-3 border-2 border-dashed rounded-lg text-gray-600 hover:border-[#b01116] hover:text-[#b01116] hover:bg-red-50 transition-all flex items-center justify-center gap-2 font-medium">
                            <i class="ri-add-circle-line text-xl"></i>
                            <span x-text="showAddSubject ? 'Batal Tambah' : 'Tambah Mata Kuliah Baru'"></span>
                        </button>
                        
                        <div x-show="showAddSubject" x-transition x-collapse class="mt-4 p-4 bg-gradient-to-br from-red-50 to-pink-50 rounded-lg border-2 border-red-200">
                            <h5 class="font-semibold text-gray-800 mb-3 flex items-center gap-2">
                                <i class="ri-add-line text-[#b01116] text-xl"></i>
                                Tambah Mata Kuliah Baru
                            </h5>
                            <div class="space-y-3">
                                <input 
                                    type="text" 
                                    x-model="newSubject.name"
                                    placeholder="Nama Mata Kuliah *"
                                    class="w-full px-4 py-2.5 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-[#b01116]">
                                <input 
                                    type="text" 
                                    x-model="newSubject.code"
                                    placeholder="Kode Mata Kuliah (opsional)"
                                    class="w-full px-4 py-2.5 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-[#b01116]">
                                <textarea 
                                    x-model="newSubject.description"
                                    placeholder="Deskripsi (opsional)"
                                    rows="2"
                                    class="w-full px-4 py-2.5 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-[#b01116] resize-none"></textarea>
                                <button 
                                    type="button"
                                    @click="createSubject()"
                                    class="w-full bg-[#b01116] hover:bg-[#8d0d11] text-white py-2.5 rounded-lg font-semibold transition-colors flex items-center justify-center gap-2 shadow-md">
                                    <i class="ri-save-line"></i>
                                    Simpan Mata Kuliah
                                </button>
                            </div>
                        </div>
                        
                        <div class="mt-4 p-3 bg-gradient-to-r from-red-50 to-pink-50 rounded-lg border border-red-200">
                            <div class="flex items-center gap-3 text-red-700">
                                <div class="w-10 h-10 rounded-full bg-[#b01116] text-white flex items-center justify-center font-bold shadow-sm">
                                    <span x-text="projectData.subjects.length"></span>
                                </div>
                                <span class="text-sm font-semibold">Mata Kuliah Dipilih</span>
                            </div>
                        </div>
                    </div>

                    <!-- TEACHERS SECTION (Similar structure) -->
                    <div>
                        <h4 class="font-semibold text-gray-800 mb-3 flex items-center gap-2 text-lg">
                            <i class="ri-user-star-line text-[#b01116]"></i>
                            Dosen/Guru Pembimbing (Opsional)
                        </h4>
                        <p class="text-sm text-gray-600 mb-4">Pilih dosen atau guru yang membimbing proyek ini</p>
                        
                        <!-- Search Box -->
                        <div class="relative mb-4">
                            <i class="ri-search-line absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 text-lg"></i>
                            <input 
                                type="text" 
                                x-model="searchTeacher"
                                placeholder="Cari nama atau NIP..."
                                class="w-full pl-11 pr-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-[#b01116] focus:border-transparent transition-all">
                        </div>
                        
                        <!-- Selection List -->
                        <div class="space-y-2 max-h-72 overflow-y-auto p-1">
                            <template x-for="teacher in filteredTeachers" :key="'modal-teach-'+teacher.id">
                                <label 
                                    class="flex items-start p-4 border-2 rounded-lg cursor-pointer hover:shadow-md transition-all duration-200 group"
                                    :class="projectData.teachers.includes(teacher.id) ? 'border-[#b01116] bg-red-50 shadow-sm' : 'border-gray-200 hover:border-gray-300'">
                                    <input 
                                        type="checkbox" 
                                        class="sr-only" 
                                        :checked="projectData.teachers.includes(teacher.id)"
                                        @change="toggleTeacher(teacher.id)">
                                    <div 
                                        class="w-5 h-5 rounded border-2 mr-3 mt-0.5 flex items-center justify-center transition-all flex-shrink-0"
                                        :class="projectData.teachers.includes(teacher.id) ? 'bg-[#b01116] border-[#b01116] scale-110' : 'border-gray-300 group-hover:border-gray-400'">
                                        <i class="ri-check-line text-white text-sm font-bold" x-show="projectData.teachers.includes(teacher.id)"></i>
                                    </div>
                                    <div class="flex-1">
                                        <div 
                                            class="font-semibold transition-colors"
                                            :class="projectData.teachers.includes(teacher.id) ? 'text-[#b01116]' : 'text-gray-800'"
                                            x-text="teacher.name"></div>
                                        <template x-if="teacher.nip">
                                            <div class="text-sm text-gray-600 mt-0.5">NIP: <span x-text="teacher.nip"></span></div>
                                        </template>
                                        <template x-if="teacher.institution">
                                            <div class="text-xs text-gray-500 mt-1 flex items-center gap-1">
                                                <i class="ri-building-line"></i>
                                                <span x-text="teacher.institution"></span>
                                            </div>
                                        </template>
                                    </div>
                                </label>
                            </template>
                        </div>
                        
                        <div x-show="filteredTeachers.length === 0" class="text-center py-12 text-gray-500">
                            <i class="ri-user-line text-5xl mb-3 opacity-50"></i>
                            <p class="font-medium">Tidak ada dosen/guru yang sesuai</p>
                        </div>
                        
                        <!-- Add New Button & Form -->
                        <button 
                            type="button"
                            @click="showAddTeacher = !showAddTeacher"
                            class="w-full mt-3 py-3 border-2 border-dashed rounded-lg text-gray-600 hover:border-[#b01116] hover:text-[#b01116] hover:bg-red-50 transition-all flex items-center justify-center gap-2 font-medium">
                            <i class="ri-add-circle-line text-xl"></i>
                            <span x-text="showAddTeacher ? 'Batal Tambah' : 'Tambah Dosen/Guru Baru'"></span>
                        </button>
                        
                        <div x-show="showAddTeacher" x-transition x-collapse class="mt-4 p-4 bg-gradient-to-br from-red-50 to-pink-50 rounded-lg border-2 border-red-200">
                            <h5 class="font-semibold text-gray-800 mb-3 flex items-center gap-2">
                                <i class="ri-add-line text-[#b01116] text-xl"></i>
                                Tambah Dosen/Guru Baru
                            </h5>
                            <div class="space-y-3">
                                <input 
                                    type="text" 
                                    x-model="newTeacher.name"
                                    placeholder="Nama Lengkap *"
                                    class="w-full px-4 py-2.5 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-[#b01116]">
                                <input 
                                    type="text" 
                                    x-model="newTeacher.nip"
                                    placeholder="NIP/NIDN (opsional)"
                                    class="w-full px-4 py-2.5 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-[#b01116]">
                                <div class="grid grid-cols-2 gap-3">
                                    <input 
                                        type="email" 
                                        x-model="newTeacher.email"
                                        placeholder="Email (opsional)"
                                        class="w-full px-4 py-2.5 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-[#b01116]">
                                    <input 
                                        type="text" 
                                        x-model="newTeacher.phone_number"
                                        placeholder="No. Telepon (opsional)"
                                        class="w-full px-4 py-2.5 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-[#b01116]">
                                </div>
                                <input 
                                    type="text" 
                                    x-model="newTeacher.institution"
                                    placeholder="Institusi/Sekolah (opsional)"
                                    class="w-full px-4 py-2.5 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-[#b01116]">
                                <button 
                                    type="button"
                                    @click="createTeacher()"
                                    class="w-full bg-[#b01116] hover:bg-[#8d0d11] text-white py-2.5 rounded-lg font-semibold transition-colors flex items-center justify-center gap-2 shadow-md">
                                    <i class="ri-save-line"></i>
                                    Simpan Dosen/Guru
                                </button>
                            </div>
                        </div>
                        
                        <div class="mt-4 p-3 bg-gradient-to-r from-red-50 to-pink-50 rounded-lg border border-red-200">
                            <div class="flex items-center gap-3 text-red-700">
                                <div class="w-10 h-10 rounded-full bg-[#8d0d11] text-white flex items-center justify-center font-bold shadow-sm">
                                    <span x-text="projectData.teachers.length"></span>
                                </div>
                                <span class="text-sm font-semibold">Pembimbing Dipilih</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Step 3: Media Upload & Review -->
                <div x-show="currentStep === 3" x-transition class="p-6 space-y-6">
                    <h3 class="text-lg font-semibold text-gray-800 flex items-center gap-2 mb-4">
                        <i class="ri-upload-cloud-2-line text-[#b01116]"></i>
                        Upload Media & Review
                    </h3>
                    
                    <!-- Media Upload -->
                    <div x-data="mediaPreview('ind_media')">
                        <label class="block text-sm font-semibold text-gray-700 mb-3">
                            Upload Media (Opsional)
                        </label>
                        <div class="border-2 border-dashed border-gray-300 rounded-xl p-8 text-center hover:border-[#b01116] hover:bg-red-50 transition-all">
                            <i class="ri-upload-cloud-2-line text-6xl text-gray-400 mb-3"></i>
                            <div class="text-sm text-gray-600 mb-3 font-medium">
                                Drop files here or click to upload
                            </div>
                            <input type="file" 
                                   name="media[]" 
                                   multiple 
                                   accept="image/*,video/*" 
                                   class="hidden" 
                                   id="ind_media"
                                   @change="handleFiles($event.target.files)">
                            <label for="ind_media" class="inline-flex items-center gap-2 cursor-pointer bg-[#b01116] text-white px-6 py-3 rounded-lg hover:bg-[#8d0d11] transition-colors font-medium shadow-md hover:shadow-lg">
                                <i class="ri-folder-open-line"></i>
                                Choose Files
                            </label>
                            <div class="text-xs text-gray-500 mt-3">
                                Max 10 files • Each up to 10MB • JPG, PNG, MP4, MOV
                            </div>
                        </div>
                        
                        <!-- Image Previews -->
                        <div x-show="previews.length > 0" class="mt-4">
                            <p class="text-sm font-medium text-gray-700 mb-3">
                                Selected Files (<span x-text="previews.length"></span>)
                                <span class="text-xs text-gray-500 ml-2">• First image will be the main image</span>
                            </p>
                            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                                <template x-for="(preview, index) in previews" :key="index">
                                    <div class="relative group">
                                        <div class="aspect-square rounded-lg overflow-hidden border-2"
                                             :class="index === 0 ? 'border-[#b01116]' : 'border-gray-300'">
                                            <img :src="preview.url" 
                                                 :alt="preview.name" 
                                                 class="w-full h-full object-cover">
                                        </div>
                                        <button type="button"
                                                @click="removeFile(index)"
                                                class="absolute top-2 right-2 bg-red-600 text-white rounded-full p-1.5 opacity-0 group-hover:opacity-100 transition-opacity hover:bg-red-700">
                                            <i class="ri-close-line text-sm"></i>
                                        </button>
                                        <div x-show="index === 0" 
                                             class="absolute bottom-0 left-0 right-0 bg-[#b01116] text-white text-xs py-1 text-center font-medium">
                                            Main Image
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>

                    <!-- Review Project Data -->
                    <div class="bg-gradient-to-br from-gray-50 to-gray-100 rounded-xl p-6 border-2 border-gray-200">
                        <h4 class="font-semibold text-gray-800 mb-4 flex items-center gap-2 text-lg">
                            <i class="ri-file-list-3-line text-[#b01116]"></i>
                            Review Proyek Anda
                        </h4>
                        <div class="space-y-3 text-sm">
                            <div class="flex items-start gap-3 p-3 bg-white rounded-lg">
                                <i class="ri-file-text-line text-gray-400 mt-0.5"></i>
                                <div class="flex-1">
                                    <span class="font-semibold text-gray-700">Judul:</span>
                                    <p class="text-gray-600 mt-1" x-text="projectData.title || 'Belum diisi'"></p>
                                </div>
                            </div>
                            <div class="flex items-start gap-3 p-3 bg-white rounded-lg">
                                <i class="ri-price-tag-3-line text-gray-400 mt-0.5"></i>
                                <div class="flex-1">
                                    <span class="font-semibold text-gray-700">Kategori:</span>
                                    <p class="text-gray-600 mt-1">
                                        <span x-text="projectData.categories.length"></span> kategori terpilih
                                        <span x-show="projectData.categories.length === 0" class="text-[#b01116] font-medium"> (Minimal 1 wajib!)</span>
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-start gap-3 p-3 bg-white rounded-lg">
                                <i class="ri-book-open-line text-gray-400 mt-0.5"></i>
                                <div class="flex-1">
                                    <span class="font-semibold text-gray-700">Mata Kuliah:</span>
                                    <p class="text-gray-600 mt-1" x-text="projectData.subjects.length + ' terpilih'"></p>
                                </div>
                            </div>
                            <div class="flex items-start gap-3 p-3 bg-white rounded-lg">
                                <i class="ri-user-star-line text-gray-400 mt-0.5"></i>
                                <div class="flex-1">
                                    <span class="font-semibold text-gray-700">Pembimbing:</span>
                                    <p class="text-gray-600 mt-1" x-text="projectData.teachers.length + ' terpilih'"></p>
                                </div>
                            </div>
                            <div class="flex items-start gap-3 p-3 bg-white rounded-lg">
                                <i class="ri-money-dollar-circle-line text-gray-400 mt-0.5"></i>
                                <div class="flex-1">
                                    <span class="font-semibold text-gray-700">Estimasi Harga:</span>
                                    <p class="text-gray-600 mt-1" x-text="projectData.price ? 'Rp ' + new Intl.NumberFormat('id-ID').format(projectData.price) : 'Tidak diisi'"></p>
                                </div>
                            </div>
                            <div class="flex items-start gap-3 p-3 bg-white rounded-lg">
                                <i class="ri-eye-line text-gray-400 mt-0.5"></i>
                                <div class="flex-1">
                                    <span class="font-semibold text-gray-700">Status:</span>
                                    <p class="text-gray-600 mt-1">
                                        <span x-show="projectData.status === 'draft'" class="inline-flex items-center gap-1 px-2 py-1 bg-gray-100 rounded text-xs font-medium">Draft</span>
                                        <span x-show="projectData.status === 'published'" class="inline-flex items-center gap-1 px-2 py-1 bg-red-100 text-red-700 rounded text-xs font-medium">Published</span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Navigation Footer -->
                <div class="sticky bottom-0 bg-white border-t-2 border-gray-200 px-6 py-4 shadow-lg">
                    <div class="flex justify-between items-center gap-4">
                        <button type="button" 
                                @click="prevStep()" 
                                x-show="currentStep > 1"
                                class="px-6 py-3 border-2 border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-semibold transition-all flex items-center gap-2">
                            <i class="ri-arrow-left-line"></i>Kembali
                        </button>
                        
                        <button type="button" 
                                @click="showIndividualProjectModal = false; resetProjectModal()" 
                                class="px-6 py-3 border-2 border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-semibold transition-all">
                            Batal
                        </button>
                        
                        <button type="button" 
                                @click="nextStep()" 
                                x-show="currentStep < totalSteps"
                                :disabled="!canProceedToNext()"
                                :class="!canProceedToNext() ? 'opacity-50 cursor-not-allowed bg-gray-400' : 'bg-[#b01116] hover:bg-[#8d0d11] shadow-md hover:shadow-lg'"
                                class="px-8 py-3 text-white rounded-lg font-semibold transition-all flex items-center gap-2">
                            <span x-show="canProceedToNext()">Selanjutnya</span>
                            <span x-show="!canProceedToNext()" x-text="getValidationMessage()"></span>
                            <i class="ri-arrow-right-line" x-show="canProceedToNext()"></i>
                        </button>
                        
                        <button type="submit" 
                                x-show="currentStep === totalSteps"
                                :class="!canCreateProject() ? 'bg-gray-400 hover:bg-gray-500' : 'bg-gradient-to-r from-[#b01116] to-[#8d0d11] hover:from-[#8d0d11] hover:to-[#b01116] shadow-lg hover:shadow-xl'"
                                class="px-8 py-3 text-white rounded-lg font-semibold transition-all flex items-center gap-2">
                            <i class="ri-save-line"></i>
                            <span>Buat Proyek</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</template>

<!-- Team Project Modal -->
<template x-teleport="body">
    <div x-show="showTeamProjectModal"
         x-transition
         @keydown.escape.window="showTeamProjectModal = false; resetProjectModal()"
         @click.self="showTeamProjectModal = false; resetProjectModal()"
         class="fixed inset-0 z-[200] bg-black/60 backdrop-blur-sm flex items-center justify-center p-4"
         role="dialog" aria-modal="true" style="display: none;">
        <div class="bg-white rounded-2xl shadow-2xl max-w-5xl w-full max-h-[90vh] overflow-y-auto" @click.stop>
            
            <!-- Modal Header with Enhanced Progress -->
            <div class="sticky top-0 bg-white border-b border-gray-200 px-6 py-5 z-10">
                <div class="flex items-center justify-between mb-5">
                    <h2 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
                        <i class="ri-team-line text-[#b01116]"></i>
                        Inisiasi Proyek Tim
                    </h2>
                    <button @click="showTeamProjectModal = false; resetProjectModal()" 
                            class="text-gray-400 hover:text-gray-600 hover:bg-gray-100 w-10 h-10 rounded-full flex items-center justify-center transition-all">
                        <i class="ri-close-line text-2xl"></i>
                    </button>
                </div>
                
                <!-- Enhanced Progress Steps -->
                <div class="flex items-center justify-center gap-2">
                    <template x-for="step in totalSteps" :key="step">
                        <div class="flex items-center gap-2">
                            <div class="relative w-11 h-11 rounded-full flex items-center justify-center text-sm font-bold transition-all duration-300 shadow-sm"
                                 :class="step < currentStep ? 'bg-[#8d0d11] text-white' : step === currentStep ? 'bg-[#b01116] text-white ring-4 ring-red-100 scale-110' : 'bg-gray-200 text-gray-500'">
                                <span x-show="step < currentStep">
                                    <i class="ri-check-line text-lg"></i>
                                </span>
                                <span x-show="step >= currentStep" x-text="step"></span>
                            </div>
                            <div x-show="step < totalSteps"
                                 class="w-20 h-1.5 transition-all duration-300 rounded-full" 
                                 :class="step < currentStep ? 'bg-[#8d0d11]' : 'bg-gray-200'"></div>
                        </div>
                    </template>
                </div>
                
                <div class="text-center mt-3 text-sm font-medium text-gray-700">
                    <span x-show="currentStep === 1" class="flex items-center justify-center gap-2">
                        <i class="ri-information-line"></i>
                        Langkah 1: Informasi Proyek Tim
                    </span>
                    <span x-show="currentStep === 2" class="flex items-center justify-center gap-2">
                        <i class="ri-price-tag-3-line"></i>
                        Langkah 2: Kategori, Mata Kuliah & Pembimbing
                    </span>
                    <span x-show="currentStep === 3" class="flex items-center justify-center gap-2">
                        <i class="ri-team-line"></i>
                        Langkah 3: Anggota Tim, Media & Review
                    </span>
                </div>
            </div>

            <!-- Modal Body -->
            <form action="{{ route('student.projects.store') }}" method="POST" enctype="multipart/form-data" novalidate>
                @csrf
                <input type="hidden" name="type" value="team">
                
                <!-- Hidden fields for form data -->
                <template x-for="(categoryId, index) in projectData.categories" :key="'team-cat-'+categoryId">
                    <input type="hidden" :name="'categories[' + index + ']'" :value="categoryId">
                </template>
                <template x-for="(subjectId, index) in projectData.subjects" :key="'team-sub-'+subjectId">
                    <input type="hidden" :name="'subjects[' + index + ']'" :value="subjectId">
                </template>
                <template x-for="(teacherId, index) in projectData.teachers" :key="'team-teach-'+teacherId">
                    <input type="hidden" :name="'teachers[' + index + ']'" :value="teacherId">
                </template>
                <template x-for="(memberId, index) in projectData.team_members" :key="'team-mem-'+memberId">
                    <input type="hidden" :name="'team_members[' + index + ']'" :value="memberId">
                </template>
                <template x-for="(position, memberId) in projectData.team_positions" :key="'team-pos-'+memberId">
                    <input type="hidden" :name="'team_positions[' + memberId + ']'" :value="position">
                </template>

                <!-- Step 1: Project Information -->
                <div x-show="currentStep === 1" x-transition class="p-6 space-y-5">
                    <h3 class="text-lg font-semibold text-gray-800 flex items-center gap-2 mb-4">
                        <i class="ri-file-text-line text-[#b01116]"></i>
                        Informasi Dasar Proyek Tim
                    </h3>
                    
                    <!-- Project Title -->
                    <div>
                        <label for="team_title" class="block text-sm font-semibold text-gray-700 mb-2 flex items-center gap-1">
                            Judul Proyek <span class="text-[#b01116]">*</span>
                        </label>
                        <input type="text" name="title" id="team_title" x-model="projectData.title" required 
                               :class="projectData.title.trim() === '' ? 'border-red-300 focus:ring-red-500 focus:border-red-500' : 'border-gray-300 focus:ring-[#b01116] focus:border-[#b01116]'"
                               class="w-full px-4 py-3 border-2 rounded-lg transition-all"
                               placeholder="Masukkan judul proyek tim yang menarik...">
                        <p x-show="projectData.title.trim() === ''" class="text-xs text-red-500 mt-1 flex items-center gap-1">
                            <i class="ri-error-warning-line"></i>
                            Judul proyek wajib diisi
                        </p>
                    </div>

                    <!-- Project Description -->
                    <div>
                        <label for="team_description" class="block text-sm font-semibold text-gray-700 mb-2 flex items-center gap-1">
                            Deskripsi Proyek
                        </label>
                        <textarea name="description" id="team_description" x-model="projectData.description" rows="6" 
                                  class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-[#b01116] focus:border-[#b01116] transition-all resize-none" 
                                  placeholder="Jelaskan detail proyek tim Anda, peran masing-masing anggota, tujuan, dan fitur utama..."></textarea>
                        <p class="text-xs text-gray-500 mt-1 flex items-center gap-1">
                            <i class="ri-lightbulb-line"></i>
                            Tips: Jelaskan bagaimana tim Anda bekerja sama untuk menciptakan proyek ini
                        </p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <!-- Project Price -->
                        <div>
                            <label for="team_price" class="block text-sm font-semibold text-gray-700 mb-2">
                                Estimasi Harga Proyek
                            </label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500 font-medium">Rp</span>
                                <input type="number" name="price" id="team_price" x-model="projectData.price" min="0" step="1000"
                                       class="w-full pl-11 pr-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-[#b01116] focus:border-[#b01116] transition-all"
                                       placeholder="0">
                            </div>
                        </div>

                        <!-- Project Status -->
                        <div>
                            <label for="team_status" class="block text-sm font-semibold text-gray-700 mb-2 flex items-center gap-1">
                                Status Publikasi <span class="text-[#b01116]">*</span>
                            </label>
                            <select name="status" id="team_status" x-model="projectData.status" required 
                                    class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-[#b01116] focus:border-[#b01116] transition-all">
                                <option value="draft">Draft</option>
                                <option value="published">Published</option>
                                <option value="archived">Archived</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Step 2: Categories, Subjects & Teachers (Same as Individual) -->
                <div x-show="currentStep === 2" x-transition class="p-6 space-y-8">
                    <!-- CATEGORIES SECTION -->
                    <div>
                        <h4 class="font-semibold text-gray-800 mb-3 flex items-center gap-2 text-lg">
                            <i class="ri-price-tag-3-line text-[#b01116]"></i>
                            Kategori Proyek <span class="text-[#b01116]">*</span>
                        </h4>
                        <p class="text-sm text-gray-600 mb-4">Pilih minimal 1 kategori yang sesuai dengan proyek tim Anda</p>
                        
                        <!-- Search Box -->
                        <div class="relative mb-4">
                            <i class="ri-search-line absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 text-lg"></i>
                            <input 
                                type="text" 
                                x-model="searchCategory"
                                placeholder="Cari kategori..."
                                class="w-full pl-11 pr-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-[#b01116] focus:border-transparent transition-all">
                        </div>
                        
                        <!-- Selection Grid -->
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-3 max-h-72 overflow-y-auto p-1">
                            <template x-for="category in filteredCategories" :key="'team-modal-cat-'+category.id">
                                <label 
                                    class="flex items-center p-3 border-2 rounded-lg cursor-pointer hover:shadow-md transition-all duration-200 group"
                                    :class="projectData.categories.includes(category.id) ? 'border-[#b01116] bg-red-50 shadow-sm' : 'border-gray-200 hover:border-gray-300'">
                                    <input 
                                        type="checkbox" 
                                        class="sr-only" 
                                        :checked="projectData.categories.includes(category.id)"
                                        @change="toggleCategory(category.id)">
                                    <div 
                                        class="w-5 h-5 rounded border-2 mr-3 flex items-center justify-center transition-all flex-shrink-0"
                                        :class="projectData.categories.includes(category.id) ? 'bg-[#b01116] border-[#b01116] scale-110' : 'border-gray-300 group-hover:border-gray-400'">
                                        <i class="ri-check-line text-white text-sm font-bold" x-show="projectData.categories.includes(category.id)"></i>
                                    </div>
                                    <span 
                                        class="text-sm font-medium transition-colors"
                                        :class="projectData.categories.includes(category.id) ? 'text-[#b01116]' : 'text-gray-700'"
                                        x-text="category.name"></span>
                                </label>
                            </template>
                        </div>
                        
                        <div x-show="filteredCategories.length === 0" class="text-center py-12 text-gray-500">
                            <i class="ri-search-line text-5xl mb-3 opacity-50"></i>
                            <p class="font-medium">Tidak ada kategori yang sesuai</p>
                            <p class="text-sm mt-1">Coba kata kunci lain atau tambahkan kategori baru</p>
                        </div>
                        
                        <!-- Add New Button -->
                        <button 
                            type="button"
                            @click="showAddCategory = !showAddCategory"
                            class="w-full mt-3 py-3 border-2 border-dashed rounded-lg text-gray-600 hover:border-[#b01116] hover:text-[#b01116] hover:bg-red-50 transition-all flex items-center justify-center gap-2 font-medium">
                            <i class="ri-add-circle-line text-xl" :class="showAddCategory ? 'rotate-45 transition-transform' : ''"></i>
                            <span x-text="showAddCategory ? 'Batal Tambah' : 'Tambah Kategori Baru'"></span>
                        </button>
                        
                        <!-- Inline Create Form -->
                        <div x-show="showAddCategory" x-transition x-collapse class="mt-4 p-4 bg-gradient-to-br from-red-50 to-pink-50 rounded-lg border-2 border-red-200">
                            <h5 class="font-semibold text-gray-800 mb-3 flex items-center gap-2">
                                <i class="ri-add-line text-[#b01116] text-xl"></i>
                                Tambah Kategori Baru
                            </h5>
                            <div class="space-y-3">
                                <input 
                                    type="text" 
                                    x-model="newCategory.name"
                                    placeholder="Nama Kategori *"
                                    class="w-full px-4 py-2.5 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-[#b01116]">
                                <textarea 
                                    x-model="newCategory.description"
                                    placeholder="Deskripsi (opsional)"
                                    rows="2"
                                    class="w-full px-4 py-2.5 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-[#b01116] resize-none"></textarea>
                                <button 
                                    type="button"
                                    @click="createCategory()"
                                    class="w-full bg-[#b01116] hover:bg-[#8d0d11] text-white py-2.5 rounded-lg font-semibold transition-colors flex items-center justify-center gap-2 shadow-md hover:shadow-lg">
                                    <i class="ri-save-line"></i>
                                    Simpan Kategori
                                </button>
                            </div>
                        </div>
                        
                        <div class="mt-4 p-3 rounded-lg border"
                             :class="projectData.categories.length === 0 ? 'bg-gradient-to-r from-red-50 to-pink-50 border-red-200' : 'bg-gradient-to-r from-red-50 to-pink-50 border-red-200'">
                            <div class="flex items-center gap-3"
                                 :class="projectData.categories.length === 0 ? 'text-red-700' : 'text-gray-700'">
                                <i :class="projectData.categories.length === 0 ? 'ri-error-warning-line' : 'ri-checkbox-circle-line'" class="text-xl"></i>
                                <div class="flex-1">
                                    <div class="font-semibold">
                                        <span x-show="projectData.categories.length === 0">Pilih minimal 1 kategori untuk melanjutkan</span>
                                        <span x-show="projectData.categories.length > 0" x-text="projectData.categories.length + ' kategori dipilih'"></span>
                                    </div>
                                    <div class="text-xs opacity-75" x-show="projectData.categories.length > 0">
                                        Kategori membantu investor menemukan proyek tim Anda
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SUBJECTS SECTION -->
                    <div>
                        <h4 class="font-semibold text-gray-800 mb-3 flex items-center gap-2 text-lg">
                            <i class="ri-book-open-line text-[#b01116]"></i>
                            Mata Kuliah (Opsional)
                        </h4>
                        <p class="text-sm text-gray-600 mb-4">Pilih mata kuliah yang berkaitan dengan proyek tim ini</p>
                        
                        <div class="relative mb-4">
                            <i class="ri-search-line absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 text-lg"></i>
                            <input 
                                type="text" 
                                x-model="searchSubject"
                                placeholder="Cari mata kuliah atau kode..."
                                class="w-full pl-11 pr-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-[#b01116] focus:border-transparent transition-all">
                        </div>
                        
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-3 max-h-72 overflow-y-auto p-1">
                            <template x-for="subject in filteredSubjects" :key="'team-modal-sub-'+subject.id">
                                <label 
                                    class="flex flex-col p-3 border-2 rounded-lg cursor-pointer hover:shadow-md transition-all duration-200 group"
                                    :class="projectData.subjects.includes(subject.id) ? 'border-[#b01116] bg-red-50 shadow-sm' : 'border-gray-200 hover:border-gray-300'">
                                    <div class="flex items-center">
                                        <input 
                                            type="checkbox" 
                                            class="sr-only" 
                                            :checked="projectData.subjects.includes(subject.id)"
                                            @change="toggleSubject(subject.id)">
                                        <div 
                                            class="w-5 h-5 rounded border-2 mr-3 flex items-center justify-center transition-all flex-shrink-0"
                                            :class="projectData.subjects.includes(subject.id) ? 'bg-[#b01116] border-[#b01116] scale-110' : 'border-gray-300 group-hover:border-gray-400'">
                                            <i class="ri-check-line text-white text-sm font-bold" x-show="projectData.subjects.includes(subject.id)"></i>
                                        </div>
                                        <span 
                                            class="text-sm font-medium transition-colors flex-1"
                                            :class="projectData.subjects.includes(subject.id) ? 'text-[#b01116]' : 'text-gray-700'"
                                            x-text="subject.name"></span>
                                    </div>
                                    <template x-if="subject.code">
                                        <span class="text-xs text-gray-500 ml-8 mt-1" x-text="'Kode: ' + subject.code"></span>
                                    </template>
                                </label>
                            </template>
                        </div>
                        
                        <div x-show="filteredSubjects.length === 0" class="text-center py-12 text-gray-500">
                            <i class="ri-book-line text-5xl mb-3 opacity-50"></i>
                            <p class="font-medium">Tidak ada mata kuliah yang sesuai</p>
                        </div>
                        
                        <button 
                            type="button"
                            @click="showAddSubject = !showAddSubject"
                            class="w-full mt-3 py-3 border-2 border-dashed rounded-lg text-gray-600 hover:border-[#b01116] hover:text-[#b01116] hover:bg-red-50 transition-all flex items-center justify-center gap-2 font-medium">
                            <i class="ri-add-circle-line text-xl"></i>
                            <span x-text="showAddSubject ? 'Batal Tambah' : 'Tambah Mata Kuliah Baru'"></span>
                        </button>
                        
                        <div x-show="showAddSubject" x-transition x-collapse class="mt-4 p-4 bg-gradient-to-br from-red-50 to-pink-50 rounded-lg border-2 border-red-200">
                            <h5 class="font-semibold text-gray-800 mb-3 flex items-center gap-2">
                                <i class="ri-add-line text-[#b01116] text-xl"></i>
                                Tambah Mata Kuliah Baru
                            </h5>
                            <div class="space-y-3">
                                <input 
                                    type="text" 
                                    x-model="newSubject.name"
                                    placeholder="Nama Mata Kuliah *"
                                    class="w-full px-4 py-2.5 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-[#b01116]">
                                <input 
                                    type="text" 
                                    x-model="newSubject.code"
                                    placeholder="Kode Mata Kuliah (opsional)"
                                    class="w-full px-4 py-2.5 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-[#b01116]">
                                <textarea 
                                    x-model="newSubject.description"
                                    placeholder="Deskripsi (opsional)"
                                    rows="2"
                                    class="w-full px-4 py-2.5 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-[#b01116] resize-none"></textarea>
                                <button 
                                    type="button"
                                    @click="createSubject()"
                                    class="w-full bg-[#b01116] hover:bg-[#8d0d11] text-white py-2.5 rounded-lg font-semibold transition-colors flex items-center justify-center gap-2 shadow-md">
                                    <i class="ri-save-line"></i>
                                    Simpan Mata Kuliah
                                </button>
                            </div>
                        </div>
                        
                        <div class="mt-4 p-3 bg-gradient-to-r from-red-50 to-pink-50 rounded-lg border border-red-200">
                            <div class="flex items-center gap-3 text-red-700">
                                <div class="w-10 h-10 rounded-full bg-[#b01116] text-white flex items-center justify-center font-bold shadow-sm">
                                    <span x-text="projectData.subjects.length"></span>
                                </div>
                                <span class="text-sm font-semibold">Mata Kuliah Dipilih</span>
                            </div>
                        </div>
                    </div>

                    <!-- TEACHERS SECTION -->
                    <div>
                        <h4 class="font-semibold text-gray-800 mb-3 flex items-center gap-2 text-lg">
                            <i class="ri-user-star-line text-[#b01116]"></i>
                            Dosen/Guru Pembimbing (Opsional)
                        </h4>
                        <p class="text-sm text-gray-600 mb-4">Pilih dosen atau guru yang membimbing proyek tim ini</p>
                        
                        <div class="relative mb-4">
                            <i class="ri-search-line absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 text-lg"></i>
                            <input 
                                type="text" 
                                x-model="searchTeacher"
                                placeholder="Cari nama atau NIP..."
                                class="w-full pl-11 pr-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-[#b01116] focus:border-transparent transition-all">
                        </div>
                        
                        <div class="space-y-2 max-h-72 overflow-y-auto p-1">
                            <template x-for="teacher in filteredTeachers" :key="'team-modal-teach-'+teacher.id">
                                <label 
                                    class="flex items-start p-4 border-2 rounded-lg cursor-pointer hover:shadow-md transition-all duration-200 group"
                                    :class="projectData.teachers.includes(teacher.id) ? 'border-[#b01116] bg-red-50 shadow-sm' : 'border-gray-200 hover:border-gray-300'">
                                    <input 
                                        type="checkbox" 
                                        class="sr-only" 
                                        :checked="projectData.teachers.includes(teacher.id)"
                                        @change="toggleTeacher(teacher.id)">
                                    <div 
                                        class="w-5 h-5 rounded border-2 mr-3 mt-0.5 flex items-center justify-center transition-all flex-shrink-0"
                                        :class="projectData.teachers.includes(teacher.id) ? 'bg-[#b01116] border-[#b01116] scale-110' : 'border-gray-300 group-hover:border-gray-400'">
                                        <i class="ri-check-line text-white text-sm font-bold" x-show="projectData.teachers.includes(teacher.id)"></i>
                                    </div>
                                    <div class="flex-1">
                                        <div 
                                            class="font-semibold transition-colors"
                                            :class="projectData.teachers.includes(teacher.id) ? 'text-[#b01116]' : 'text-gray-800'"
                                            x-text="teacher.name"></div>
                                        <template x-if="teacher.nip">
                                            <div class="text-sm text-gray-600 mt-0.5">NIP: <span x-text="teacher.nip"></span></div>
                                        </template>
                                        <template x-if="teacher.institution">
                                            <div class="text-xs text-gray-500 mt-1 flex items-center gap-1">
                                                <i class="ri-building-line"></i>
                                                <span x-text="teacher.institution"></span>
                                            </div>
                                        </template>
                                    </div>
                                </label>
                            </template>
                        </div>
                        
                        <div x-show="filteredTeachers.length === 0" class="text-center py-12 text-gray-500">
                            <i class="ri-user-line text-5xl mb-3 opacity-50"></i>
                            <p class="font-medium">Tidak ada dosen/guru yang sesuai</p>
                        </div>
                        
                        <button 
                            type="button"
                            @click="showAddTeacher = !showAddTeacher"
                            class="w-full mt-3 py-3 border-2 border-dashed rounded-lg text-gray-600 hover:border-[#b01116] hover:text-[#b01116] hover:bg-red-50 transition-all flex items-center justify-center gap-2 font-medium">
                            <i class="ri-add-circle-line text-xl"></i>
                            <span x-text="showAddTeacher ? 'Batal Tambah' : 'Tambah Dosen/Guru Baru'"></span>
                        </button>
                        
                        <div x-show="showAddTeacher" x-transition x-collapse class="mt-4 p-4 bg-gradient-to-br from-blue-50 to-indigo-50 rounded-lg border-2 border-blue-200">
                            <h5 class="font-semibold text-gray-800 mb-3 flex items-center gap-2">
                                <i class="ri-add-line text-[#b01116] text-xl"></i>
                                Tambah Dosen/Guru Baru
                            </h5>
                            <div class="space-y-3">
                                <input 
                                    type="text" 
                                    x-model="newTeacher.name"
                                    placeholder="Nama Lengkap *"
                                    class="w-full px-4 py-2.5 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-[#b01116]">
                                <input 
                                    type="text" 
                                    x-model="newTeacher.nip"
                                    placeholder="NIP/NIDN (opsional)"
                                    class="w-full px-4 py-2.5 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-[#b01116]">
                                <div class="grid grid-cols-2 gap-3">
                                    <input 
                                        type="email" 
                                        x-model="newTeacher.email"
                                        placeholder="Email (opsional)"
                                        class="w-full px-4 py-2.5 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-[#b01116]">
                                    <input 
                                        type="text" 
                                        x-model="newTeacher.phone_number"
                                        placeholder="No. Telepon (opsional)"
                                        class="w-full px-4 py-2.5 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-[#b01116]">
                                </div>
                                <input 
                                    type="text" 
                                    x-model="newTeacher.institution"
                                    placeholder="Institusi/Sekolah (opsional)"
                                    class="w-full px-4 py-2.5 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-[#b01116]">
                                <button 
                                    type="button"
                                    @click="createTeacher()"
                                    class="w-full bg-[#b01116] hover:bg-[#8d0d11] text-white py-2.5 rounded-lg font-semibold transition-colors flex items-center justify-center gap-2 shadow-md">
                                    <i class="ri-save-line"></i>
                                    Simpan Dosen/Guru
                                </button>
                            </div>
                        </div>
                        
                        <div class="mt-4 p-3 bg-gradient-to-r from-red-50 to-pink-50 rounded-lg border border-red-200">
                            <div class="flex items-center gap-3 text-red-700">
                                <div class="w-10 h-10 rounded-full bg-[#8d0d11] text-white flex items-center justify-center font-bold shadow-sm">
                                    <span x-text="projectData.teachers.length"></span>
                                </div>
                                <span class="text-sm font-semibold">Pembimbing Dipilih</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Step 3: Team Members, Media & Review -->
                <div x-show="currentStep === 3" x-transition class="p-6 space-y-6">
                    <!-- TEAM MEMBERS SECTION -->
                    <div>
                        <h4 class="font-semibold text-gray-800 mb-3 flex items-center gap-2 text-lg">
                            <i class="ri-team-line text-[#b01116]"></i>
                            Anggota Tim <span class="text-[#b01116]">*</span>
                        </h4>
                        <p class="text-sm text-gray-600 mb-4">Anda akan menjadi leader tim secara otomatis. Pilih anggota tim lainnya:</p>
                        
                        <!-- Search Box -->
                        <div class="relative mb-4">
                            <i class="ri-search-line absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 text-lg"></i>
                            <input 
                                type="text" 
                                x-model="searchStudent"
                                placeholder="Cari mahasiswa berdasarkan nama, username, atau NIM..."
                                class="w-full pl-11 pr-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-[#b01116] focus:border-transparent transition-all">
                        </div>
                        
                        <!-- Team Members List -->
                        <div class="space-y-3 max-h-96 overflow-y-auto p-1">
                            <template x-for="student in filteredStudents" :key="'team-student-'+student.id">
                                <div 
                                    class="p-4 border-2 rounded-lg transition-all duration-200"
                                    :class="projectData.team_members.includes(student.id) ? 'border-[#b01116] bg-red-50 shadow-sm' : 'border-gray-200 hover:border-gray-300'">
                                    
                                    <!-- Student Info with Checkbox -->
                                    <label class="flex items-start gap-3 cursor-pointer">
                                        <input 
                                            type="checkbox" 
                                            class="sr-only" 
                                            :checked="projectData.team_members.includes(student.id)"
                                            @change="toggleTeamMember(student.id)">
                                        
                                        <div 
                                            class="w-5 h-5 rounded border-2 flex-shrink-0 mt-1 flex items-center justify-center transition-all"
                                            :class="projectData.team_members.includes(student.id) ? 'bg-[#b01116] border-[#b01116] scale-110' : 'border-gray-300'">
                                            <i class="ri-check-line text-white text-sm font-bold" x-show="projectData.team_members.includes(student.id)"></i>
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
                                        class="mt-4 pl-8 space-y-2">
                                        <label class="block text-sm font-semibold text-gray-700">
                                            Posisi dalam Tim <span class="text-[#b01116]">*</span>
                                        </label>
                                        <input 
                                            type="text" 
                                            x-model="projectData.team_positions[student.id]"
                                            placeholder="Contoh: Frontend Developer, UI Designer, Project Manager, Data Analyst"
                                            class="w-full px-3 py-2.5 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-[#b01116] focus:border-transparent text-sm">
                                        <p class="text-xs text-gray-500 flex items-center gap-1">
                                            <i class="ri-information-line"></i>
                                            Jelaskan peran dan tanggung jawab spesifik anggota ini dalam tim
                                        </p>
                                    </div>
                                </div>
                            </template>
                        </div>
                        
                        <div x-show="filteredStudents.length === 0" class="text-center py-12 text-gray-500">
                            <i class="ri-user-search-line text-5xl mb-3 opacity-50"></i>
                            <p class="font-medium">Tidak ada mahasiswa yang sesuai</p>
                            <p class="text-sm mt-1">Coba kata kunci lain</p>
                        </div>
                        
                        <div class="mt-4 p-3 rounded-lg border"
                             :class="projectData.team_members.length < 1 ? 'bg-gradient-to-r from-red-50 to-pink-50 border-red-200' : 'bg-gradient-to-r from-gray-50 to-gray-100 border-gray-200'">
                            <div class="flex items-center gap-3"
                                 :class="projectData.team_members.length < 1 ? 'text-red-700' : 'text-gray-700'">
                                <i :class="projectData.team_members.length < 1 ? 'ri-error-warning-line' : 'ri-team-line'" class="text-xl"></i>
                                <div class="flex-1">
                                    <div class="font-semibold">
                                        <span x-show="projectData.team_members.length < 1">Pilih minimal 1 anggota tim untuk melanjutkan</span>
                                        <span x-show="projectData.team_members.length >= 1" x-text="(projectData.team_members.length + 1) + ' total anggota tim'"></span>
                                    </div>
                                    <div class="text-xs opacity-75">
                                        <span x-show="projectData.team_members.length < 1">Tim minimal terdiri dari 2 orang (leader + 1 anggota)</span>
                                        <span x-show="projectData.team_members.length >= 1">Termasuk Anda sebagai leader</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Media Upload -->
                    <div x-data="mediaPreview('team_media')">
                        <label class="block text-sm font-semibold text-gray-700 mb-3">
                            Upload Media (Opsional)
                        </label>
                        <div class="border-2 border-dashed border-gray-300 rounded-xl p-8 text-center hover:border-[#b01116] hover:bg-red-50 transition-all">
                            <i class="ri-upload-cloud-2-line text-6xl text-gray-400 mb-3"></i>
                            <div class="text-sm text-gray-600 mb-3 font-medium">
                                Drop files here or click to upload
                            </div>
                            <input type="file" 
                                   name="media[]" 
                                   multiple 
                                   accept="image/*,video/*" 
                                   class="hidden" 
                                   id="team_media"
                                   @change="handleFiles($event.target.files)">
                            <label for="team_media" class="inline-flex items-center gap-2 cursor-pointer bg-[#b01116] text-white px-6 py-3 rounded-lg hover:bg-[#8d0d11] transition-colors font-medium shadow-md hover:shadow-lg">
                                <i class="ri-folder-open-line"></i>
                                Choose Files
                            </label>
                            <div class="text-xs text-gray-500 mt-3">
                                Max 10 files • Each up to 10MB • JPG, PNG, MP4, MOV
                            </div>
                        </div>
                        
                        <!-- Image Previews -->
                        <div x-show="previews.length > 0" class="mt-4">
                            <p class="text-sm font-medium text-gray-700 mb-3">
                                Selected Files (<span x-text="previews.length"></span>)
                                <span class="text-xs text-gray-500 ml-2">• First image will be the main image</span>
                            </p>
                            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                                <template x-for="(preview, index) in previews" :key="index">
                                    <div class="relative group">
                                        <div class="aspect-square rounded-lg overflow-hidden border-2"
                                             :class="index === 0 ? 'border-[#b01116]' : 'border-gray-300'">
                                            <img :src="preview.url" 
                                                 :alt="preview.name" 
                                                 class="w-full h-full object-cover">
                                        </div>
                                        <button type="button"
                                                @click="removeFile(index)"
                                                class="absolute top-2 right-2 bg-red-600 text-white rounded-full p-1.5 opacity-0 group-hover:opacity-100 transition-opacity hover:bg-red-700">
                                            <i class="ri-close-line text-sm"></i>
                                        </button>
                                        <div x-show="index === 0" 
                                             class="absolute bottom-0 left-0 right-0 bg-[#b01116] text-white text-xs py-1 text-center font-medium">
                                            Main Image
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>

                    <!-- Review Project Data -->
                    <div class="bg-gradient-to-br from-gray-50 to-gray-100 rounded-xl p-6 border-2 border-gray-200">
                        <h4 class="font-semibold text-gray-800 mb-4 flex items-center gap-2 text-lg">
                            <i class="ri-file-list-3-line text-[#b01116]"></i>
                            Review Proyek Tim Anda
                        </h4>
                        <div class="space-y-3 text-sm">
                            <div class="flex items-start gap-3 p-3 bg-white rounded-lg">
                                <i class="ri-file-text-line text-gray-400 mt-0.5"></i>
                                <div class="flex-1">
                                    <span class="font-semibold text-gray-700">Judul:</span>
                                    <p class="text-gray-600 mt-1" x-text="projectData.title || 'Belum diisi'"></p>
                                </div>
                            </div>
                            <div class="flex items-start gap-3 p-3 bg-white rounded-lg">
                                <i class="ri-price-tag-3-line text-gray-400 mt-0.5"></i>
                                <div class="flex-1">
                                    <span class="font-semibold text-gray-700">Kategori:</span>
                                    <p class="text-gray-600 mt-1">
                                        <span x-text="projectData.categories.length"></span> kategori terpilih
                                        <span x-show="projectData.categories.length === 0" class="text-[#b01116] font-medium"> (Minimal 1 wajib!)</span>
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-start gap-3 p-3 bg-white rounded-lg">
                                <i class="ri-book-open-line text-gray-400 mt-0.5"></i>
                                <div class="flex-1">
                                    <span class="font-semibold text-gray-700">Mata Kuliah:</span>
                                    <p class="text-gray-600 mt-1" x-text="projectData.subjects.length + ' terpilih'"></p>
                                </div>
                            </div>
                            <div class="flex items-start gap-3 p-3 bg-white rounded-lg">
                                <i class="ri-user-star-line text-gray-400 mt-0.5"></i>
                                <div class="flex-1">
                                    <span class="font-semibold text-gray-700">Pembimbing:</span>
                                    <p class="text-gray-600 mt-1" x-text="projectData.teachers.length + ' terpilih'"></p>
                                </div>
                            </div>
                            <div class="flex items-start gap-3 p-3 bg-white rounded-lg">
                                <i class="ri-team-line text-gray-400 mt-0.5"></i>
                                <div class="flex-1">
                                    <span class="font-semibold text-gray-700">Anggota Tim:</span>
                                    <p class="text-gray-600 mt-1">
                                        <span x-text="projectData.team_members.length + 1"></span> orang (termasuk Anda sebagai leader)
                                        <span x-show="projectData.team_members.length === 0" class="text-[#b01116] font-medium"> (Minimal 1 anggota tambahan!)</span>
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-start gap-3 p-3 bg-white rounded-lg">
                                <i class="ri-money-dollar-circle-line text-gray-400 mt-0.5"></i>
                                <div class="flex-1">
                                    <span class="font-semibold text-gray-700">Estimasi Harga:</span>
                                    <p class="text-gray-600 mt-1" x-text="projectData.price ? 'Rp ' + new Intl.NumberFormat('id-ID').format(projectData.price) : 'Tidak diisi'"></p>
                                </div>
                            </div>
                            <div class="flex items-start gap-3 p-3 bg-white rounded-lg">
                                <i class="ri-eye-line text-gray-400 mt-0.5"></i>
                                <div class="flex-1">
                                    <span class="font-semibold text-gray-700">Status:</span>
                                    <p class="text-gray-600 mt-1">
                                        <span x-show="projectData.status === 'draft'" class="inline-flex items-center gap-1 px-2 py-1 bg-gray-100 rounded text-xs font-medium">Draft</span>
                                        <span x-show="projectData.status === 'published'" class="inline-flex items-center gap-1 px-2 py-1 bg-green-100 text-green-700 rounded text-xs font-medium">Published</span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Navigation Footer -->
                <div class="sticky bottom-0 bg-white border-t-2 border-gray-200 px-6 py-4 shadow-lg">
                    <div class="flex justify-between items-center gap-4">
                        <button type="button" 
                                @click="prevStep()" 
                                x-show="currentStep > 1"
                                class="px-6 py-3 border-2 border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-semibold transition-all flex items-center gap-2">
                            <i class="ri-arrow-left-line"></i>Kembali
                        </button>
                        
                        <button type="button" 
                                @click="showTeamProjectModal = false; resetProjectModal()" 
                                class="px-6 py-3 border-2 border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-semibold transition-all">
                            Batal
                        </button>
                        
                        <button type="button" 
                                @click="nextStep()" 
                                x-show="currentStep < totalSteps"
                                :disabled="!canProceedToNext()"
                                :class="!canProceedToNext() ? 'opacity-50 cursor-not-allowed bg-gray-400' : 'bg-[#b01116] hover:bg-[#8d0d11] shadow-md hover:shadow-lg'"
                                class="px-8 py-3 text-white rounded-lg font-semibold transition-all flex items-center gap-2">
                            <span x-show="canProceedToNext()">Selanjutnya</span>
                            <span x-show="!canProceedToNext()" x-text="getValidationMessage()"></span>
                            <i class="ri-arrow-right-line" x-show="canProceedToNext()"></i>
                        </button>
                        
                        <button type="submit" 
                                x-show="currentStep === totalSteps"
                                :class="!canCreateProject() ? 'bg-gray-400 hover:bg-gray-500' : 'bg-gradient-to-r from-[#b01116] to-[#8d0d11] hover:from-[#8d0d11] hover:to-[#b01116] shadow-lg hover:shadow-xl'"
                                class="px-8 py-3 text-white rounded-lg font-semibold transition-all flex items-center gap-2">
                            <i class="ri-save-line"></i>
                            <span>Inisiasi Proyek Tim</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</template>

<!-- Edit Project Modal (Works for both Individual and Team) -->
<template x-teleport="body">
    <div x-show="showEditProjectModal"
         x-transition
         @keydown.escape.window="showEditProjectModal = false; resetProjectModal()"
         @click.self="showEditProjectModal = false; resetProjectModal()"
         class="fixed inset-0 z-[200] bg-black/60 backdrop-blur-sm flex items-center justify-center p-4"
         role="dialog" aria-modal="true" style="display: none;">
        <div class="bg-white rounded-2xl shadow-2xl max-w-5xl w-full max-h-[90vh] overflow-y-auto" @click.stop>
            
            <!-- Modal Header -->
            <div class="sticky top-0 bg-white border-b border-gray-200 px-6 py-5 z-10">
                <div class="flex items-center justify-between mb-5">
                    <h2 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
                        <i class="ri-edit-line text-[#b01116]"></i>
                        <span>Edit Proyek <span x-text="projectType === 'team' ? 'Tim' : 'Individu'" class="text-[#b01116]"></span></span>
                    </h2>
                    <button @click="showEditProjectModal = false; resetProjectModal()" 
                            class="text-gray-400 hover:text-gray-600 hover:bg-gray-100 w-10 h-10 rounded-full flex items-center justify-center transition-all">
                        <i class="ri-close-line text-2xl"></i>
                    </button>
                </div>
                
                <!-- Progress Steps -->
                <div class="flex items-center justify-center gap-2">
                    <template x-for="step in totalSteps" :key="step">
                        <div class="flex items-center gap-2">
                            <div class="relative w-11 h-11 rounded-full flex items-center justify-center text-sm font-bold transition-all duration-300 shadow-sm"
                                 :class="step < currentStep ? 'bg-[#8d0d11] text-white' : step === currentStep ? 'bg-[#b01116] text-white ring-4 ring-red-100 scale-110' : 'bg-gray-200 text-gray-500'">
                                <span x-show="step < currentStep">
                                    <i class="ri-check-line text-lg"></i>
                                </span>
                                <span x-show="step >= currentStep" x-text="step"></span>
                            </div>
                            <div x-show="step < totalSteps"
                                 class="w-20 h-1.5 transition-all duration-300 rounded-full" 
                                 :class="step < currentStep ? 'bg-[#8d0d11]' : 'bg-gray-200'"></div>
                        </div>
                    </template>
                </div>
                
                <div class="text-center mt-3 text-sm font-medium text-gray-700">
                    <span x-show="currentStep === 1" class="flex items-center justify-center gap-2">
                        <i class="ri-information-line"></i>
                        Langkah 1: Informasi Proyek
                    </span>
                    <span x-show="currentStep === 2" class="flex items-center justify-center gap-2">
                        <i class="ri-price-tag-3-line"></i>
                        Langkah 2: Kategori, Mata Kuliah & Pembimbing
                    </span>
                    <span x-show="currentStep === 3" class="flex items-center justify-center gap-2">
                        <i class="ri-upload-cloud-2-line"></i>
                        <span x-text="projectType === 'team' ? 'Langkah 3: Anggota Tim & Review' : 'Langkah 3: Media & Review'"></span>
                    </span>
                </div>
            </div>

            <!-- Modal Body -->
            <div>
                <!-- Step 1: Project Information -->
                <div x-show="currentStep === 1" x-transition class="p-6 space-y-5">
                    <h3 class="text-lg font-semibold text-gray-800 flex items-center gap-2 mb-4">
                        <i class="ri-file-text-line text-[#b01116]"></i>
                        Informasi Dasar Proyek
                    </h3>
                    
                    <!-- Project Title -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2 flex items-center gap-1">
                            Judul Proyek <span class="text-[#b01116]">*</span>
                        </label>
                        <input type="text" x-model="projectData.title" required 
                               :class="projectData.title.trim() === '' ? 'border-red-300 focus:ring-red-500 focus:border-red-500' : 'border-gray-300 focus:ring-[#b01116] focus:border-[#b01116]'"
                               class="w-full px-4 py-3 border-2 rounded-lg transition-all"
                               placeholder="Masukkan judul proyek yang menarik...">
                        <p x-show="projectData.title.trim() === ''" class="text-xs text-red-500 mt-1 flex items-center gap-1">
                            <i class="ri-error-warning-line"></i>
                            Judul proyek wajib diisi
                        </p>
                    </div>

                    <!-- Project Description -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2 flex items-center gap-1">
                            Deskripsi Proyek
                        </label>
                        <textarea x-model="projectData.description" rows="6" 
                                  class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-[#b01116] focus:border-[#b01116] transition-all resize-none" 
                                  placeholder="Jelaskan detail proyek Anda, tujuan, fitur utama, dan hal menarik lainnya..."></textarea>
                        <p class="text-xs text-gray-500 mt-1 flex items-center gap-1">
                            <i class="ri-lightbulb-line"></i>
                            Tips: Deskripsi yang detail akan menarik lebih banyak investor
                        </p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <!-- Project Price -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Estimasi Harga (Rp)
                            </label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500 font-medium">Rp</span>
                                <input type="number" x-model="projectData.price" min="0" step="1000"
                                       class="w-full pl-11 pr-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-[#b01116] focus:border-[#b01116] transition-all"
                                       placeholder="0">
                            </div>
                        </div>

                        <!-- Project Status -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2 flex items-center gap-1">
                                Status Publikasi <span class="text-[#b01116]">*</span>
                            </label>
                            <select x-model="projectData.status" required 
                                    class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-[#b01116] focus:border-[#b01116] transition-all">
                                <option value="draft">Draft</option>
                                <option value="published">Published</option>
                                <option value="archived">Archived</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Step 2: Categories, Subjects & Teachers -->
                <div x-show="currentStep === 2" x-transition class="p-6 space-y-8">
                    <!-- CATEGORIES SECTION -->
                    <div>
                        <h4 class="font-semibold text-gray-800 mb-3 flex items-center gap-2 text-lg">
                            <i class="ri-price-tag-3-line text-[#b01116]"></i>
                            Kategori Proyek <span class="text-[#b01116]">*</span>
                        </h4>
                        <p class="text-sm text-gray-600 mb-4">Edit kategori proyek Anda</p>
                        
                        <!-- Search Box -->
                        <div class="relative mb-4">
                            <i class="ri-search-line absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 text-lg"></i>
                            <input 
                                type="text" 
                                x-model="searchCategory"
                                placeholder="Cari kategori..."
                                class="w-full pl-11 pr-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-[#b01116] focus:border-transparent transition-all">
                        </div>
                        
                        <!-- Selection Grid -->
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-3 max-h-72 overflow-y-auto p-1">
                            <template x-for="category in filteredCategories" :key="category.id">
                                <label 
                                    class="flex items-center p-3 border-2 rounded-lg cursor-pointer hover:shadow-md transition-all duration-200 group"
                                    :class="projectData.categories.includes(category.id) ? 'border-[#b01116] bg-red-50 shadow-sm' : 'border-gray-200 hover:border-gray-300'">
                                    <input 
                                        type="checkbox" 
                                        class="sr-only" 
                                        :checked="projectData.categories.includes(category.id)"
                                        @change="toggleCategory(category.id)">
                                    <div 
                                        class="w-5 h-5 rounded border-2 mr-3 flex items-center justify-center transition-all flex-shrink-0"
                                        :class="projectData.categories.includes(category.id) ? 'bg-[#b01116] border-[#b01116] scale-110' : 'border-gray-300 group-hover:border-gray-400'">
                                        <i class="ri-check-line text-white text-sm font-bold" x-show="projectData.categories.includes(category.id)"></i>
                                    </div>
                                    <span 
                                        class="text-sm font-medium transition-colors"
                                        :class="projectData.categories.includes(category.id) ? 'text-[#b01116]' : 'text-gray-700'"
                                        x-text="category.name"></span>
                                </label>
                            </template>
                        </div>
                        
                        <!-- No Results -->
                        <div x-show="filteredCategories.length === 0" class="text-center py-12 text-gray-500">
                            <i class="ri-search-line text-5xl mb-3 opacity-50"></i>
                            <p class="font-medium">Tidak ada kategori yang sesuai</p>
                            <p class="text-sm mt-1">Coba kata kunci lain atau tambahkan kategori baru</p>
                        </div>
                        
                        <!-- Add New Button -->
                        <button 
                            type="button"
                            @click="showAddCategory = !showAddCategory"
                            class="w-full mt-3 py-3 border-2 border-dashed rounded-lg text-gray-600 hover:border-[#b01116] hover:text-[#b01116] hover:bg-red-50 transition-all flex items-center justify-center gap-2 font-medium">
                            <i class="ri-add-circle-line text-xl" :class="showAddCategory ? 'rotate-45 transition-transform' : ''"></i>
                            <span x-text="showAddCategory ? 'Batal Tambah' : 'Tambah Kategori Baru'"></span>
                        </button>
                        
                        <!-- Inline Create Form -->
                        <div x-show="showAddCategory" x-transition x-collapse class="mt-4 p-4 bg-gradient-to-br from-red-50 to-pink-50 rounded-lg border-2 border-red-200">
                            <h5 class="font-semibold text-gray-800 mb-3 flex items-center gap-2">
                                <i class="ri-add-line text-[#b01116] text-xl"></i>
                                Tambah Kategori Baru
                            </h5>
                            <div class="space-y-3">
                                <input 
                                    type="text" 
                                    x-model="newCategory.name"
                                    placeholder="Nama Kategori *"
                                    class="w-full px-4 py-2.5 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-[#b01116] focus:border-transparent">
                                <textarea 
                                    x-model="newCategory.description"
                                    placeholder="Deskripsi (opsional)"
                                    rows="2"
                                    class="w-full px-4 py-2.5 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-[#b01116] focus:border-transparent resize-none"></textarea>
                                <button 
                                    type="button"
                                    @click="createCategory()"
                                    class="w-full bg-[#b01116] hover:bg-[#8d0d11] text-white py-2.5 rounded-lg font-semibold transition-colors flex items-center justify-center gap-2 shadow-md hover:shadow-lg">
                                    <i class="ri-save-line"></i>
                                    Simpan Kategori
                                </button>
                            </div>
                        </div>
                        
                        <!-- Selection Counter -->
                        <div class="mt-4 p-3 rounded-lg border"
                             :class="projectData.categories.length === 0 ? 'bg-gradient-to-r from-red-50 to-pink-50 border-red-200' : 'bg-gradient-to-r from-red-50 to-pink-50 border-red-200'">
                            <div class="flex items-center gap-3"
                                 :class="projectData.categories.length === 0 ? 'text-red-700' : 'text-gray-700'">
                                <i :class="projectData.categories.length === 0 ? 'ri-error-warning-line' : 'ri-checkbox-circle-line'" class="text-xl"></i>
                                <div class="flex-1">
                                    <div class="font-semibold">
                                        <span x-show="projectData.categories.length === 0">Pilih minimal 1 kategori untuk melanjutkan</span>
                                        <span x-show="projectData.categories.length > 0" x-text="projectData.categories.length + ' kategori dipilih'"></span>
                                    </div>
                                    <div class="text-xs opacity-75" x-show="projectData.categories.length > 0">
                                        Kategori membantu investor menemukan proyek Anda
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SUBJECTS SECTION (Similar structure) -->
                    <div>
                        <h4 class="font-semibold text-gray-800 mb-3 flex items-center gap-2 text-lg">
                            <i class="ri-book-open-line text-[#b01116]"></i>
                            Mata Kuliah (Opsional)
                        </h4>
                        <p class="text-sm text-gray-600 mb-4">Edit mata kuliah yang berkaitan</p>
                        
                        <!-- Search Box -->
                        <div class="relative mb-4">
                            <i class="ri-search-line absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 text-lg"></i>
                            <input 
                                type="text" 
                                x-model="searchSubject"
                                placeholder="Cari mata kuliah atau kode..."
                                class="w-full pl-11 pr-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-[#b01116] focus:border-transparent transition-all">
                        </div>
                        
                        <!-- Selection Grid -->
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-3 max-h-72 overflow-y-auto p-1">
                            <template x-for="subject in filteredSubjects" :key="subject.id">
                                <label 
                                    class="flex flex-col p-3 border-2 rounded-lg cursor-pointer hover:shadow-md transition-all duration-200 group"
                                    :class="projectData.subjects.includes(subject.id) ? 'border-[#b01116] bg-red-50 shadow-sm' : 'border-gray-200 hover:border-gray-300'">
                                    <div class="flex items-center">
                                        <input 
                                            type="checkbox" 
                                            class="sr-only" 
                                            :checked="projectData.subjects.includes(subject.id)"
                                            @change="toggleSubject(subject.id)">
                                        <div 
                                            class="w-5 h-5 rounded border-2 mr-3 flex items-center justify-center transition-all flex-shrink-0"
                                            :class="projectData.subjects.includes(subject.id) ? 'bg-[#b01116] border-[#b01116] scale-110' : 'border-gray-300 group-hover:border-gray-400'">
                                            <i class="ri-check-line text-white text-sm font-bold" x-show="projectData.subjects.includes(subject.id)"></i>
                                        </div>
                                        <span 
                                            class="text-sm font-medium transition-colors flex-1"
                                            :class="projectData.subjects.includes(subject.id) ? 'text-[#b01116]' : 'text-gray-700'"
                                            x-text="subject.name"></span>
                                    </div>
                                    <template x-if="subject.code">
                                        <span class="text-xs text-gray-500 ml-8 mt-1" x-text="'Kode: ' + subject.code"></span>
                                    </template>
                                </label>
                            </template>
                        </div>
                        
                        <div x-show="filteredSubjects.length === 0" class="text-center py-12 text-gray-500">
                            <i class="ri-book-line text-5xl mb-3 opacity-50"></i>
                            <p class="font-medium">Tidak ada mata kuliah yang sesuai</p>
                        </div>
                        
                        <!-- Add New Button & Form (Similar to categories) -->
                        <button 
                            type="button"
                            @click="showAddSubject = !showAddSubject"
                            class="w-full mt-3 py-3 border-2 border-dashed rounded-lg text-gray-600 hover:border-[#b01116] hover:text-[#b01116] hover:bg-red-50 transition-all flex items-center justify-center gap-2 font-medium">
                            <i class="ri-add-circle-line text-xl"></i>
                            <span x-text="showAddSubject ? 'Batal Tambah' : 'Tambah Mata Kuliah Baru'"></span>
                        </button>
                        
                        <div x-show="showAddSubject" x-transition x-collapse class="mt-4 p-4 bg-gradient-to-br from-red-50 to-pink-50 rounded-lg border-2 border-red-200">
                            <h5 class="font-semibold text-gray-800 mb-3 flex items-center gap-2">
                                <i class="ri-add-line text-[#b01116] text-xl"></i>
                                Tambah Mata Kuliah Baru
                            </h5>
                            <div class="space-y-3">
                                <input 
                                    type="text" 
                                    x-model="newSubject.name"
                                    placeholder="Nama Mata Kuliah *"
                                    class="w-full px-4 py-2.5 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-[#b01116]">
                                <input 
                                    type="text" 
                                    x-model="newSubject.code"
                                    placeholder="Kode Mata Kuliah (opsional)"
                                    class="w-full px-4 py-2.5 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-[#b01116]">
                                <textarea 
                                    x-model="newSubject.description"
                                    placeholder="Deskripsi (opsional)"
                                    rows="2"
                                    class="w-full px-4 py-2.5 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-[#b01116] resize-none"></textarea>
                                <button 
                                    type="button"
                                    @click="createSubject()"
                                    class="w-full bg-[#b01116] hover:bg-[#8d0d11] text-white py-2.5 rounded-lg font-semibold transition-colors flex items-center justify-center gap-2 shadow-md">
                                    <i class="ri-save-line"></i>
                                    Simpan Mata Kuliah
                                </button>
                            </div>
                        </div>
                        
                        <div class="mt-4 p-3 bg-gradient-to-r from-red-50 to-pink-50 rounded-lg border border-red-200">
                            <div class="flex items-center gap-3 text-red-700">
                                <div class="w-10 h-10 rounded-full bg-[#b01116] text-white flex items-center justify-center font-bold shadow-sm">
                                    <span x-text="projectData.subjects.length"></span>
                                </div>
                                <span class="text-sm font-semibold">Mata Kuliah Dipilih</span>
                            </div>
                        </div>
                    </div>

                    <!-- TEACHERS SECTION (Similar structure) -->
                    <div>
                        <h4 class="font-semibold text-gray-800 mb-3 flex items-center gap-2 text-lg">
                            <i class="ri-user-star-line text-[#b01116]"></i>
                            Dosen/Guru Pembimbing (Opsional)
                        </h4>
                        <p class="text-sm text-gray-600 mb-4">Edit dosen atau guru pembimbing</p>
                        
                        <!-- Search Box -->
                        <div class="relative mb-4">
                            <i class="ri-search-line absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 text-lg"></i>
                            <input 
                                type="text" 
                                x-model="searchTeacher"
                                placeholder="Cari nama atau NIP..."
                                class="w-full pl-11 pr-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-[#b01116] focus:border-transparent transition-all">
                        </div>
                        
                        <!-- Selection List -->
                        <div class="space-y-2 max-h-72 overflow-y-auto p-1">
                            <template x-for="teacher in filteredTeachers" :key="teacher.id">
                                <label 
                                    class="flex items-start p-4 border-2 rounded-lg cursor-pointer hover:shadow-md transition-all duration-200 group"
                                    :class="projectData.teachers.includes(teacher.id) ? 'border-[#b01116] bg-red-50 shadow-sm' : 'border-gray-200 hover:border-gray-300'">
                                    <input 
                                        type="checkbox" 
                                        class="sr-only" 
                                        :checked="projectData.teachers.includes(teacher.id)"
                                        @change="toggleTeacher(teacher.id)">
                                    <div 
                                        class="w-5 h-5 rounded border-2 mr-3 mt-0.5 flex items-center justify-center transition-all flex-shrink-0"
                                        :class="projectData.teachers.includes(teacher.id) ? 'bg-[#b01116] border-[#b01116] scale-110' : 'border-gray-300 group-hover:border-gray-400'">
                                        <i class="ri-check-line text-white text-sm font-bold" x-show="projectData.teachers.includes(teacher.id)"></i>
                                    </div>
                                    <div class="flex-1">
                                        <div 
                                            class="font-semibold transition-colors"
                                            :class="projectData.teachers.includes(teacher.id) ? 'text-[#b01116]' : 'text-gray-800'"
                                            x-text="teacher.name"></div>
                                        <template x-if="teacher.nip">
                                            <div class="text-sm text-gray-600 mt-0.5">NIP: <span x-text="teacher.nip"></span></div>
                                        </template>
                                        <template x-if="teacher.institution">
                                            <div class="text-xs text-gray-500 mt-1 flex items-center gap-1">
                                                <i class="ri-building-line"></i>
                                                <span x-text="teacher.institution"></span>
                                            </div>
                                        </template>
                                    </div>
                                </label>
                            </template>
                        </div>
                        
                        <div x-show="filteredTeachers.length === 0" class="text-center py-12 text-gray-500">
                            <i class="ri-user-line text-5xl mb-3 opacity-50"></i>
                            <p class="font-medium">Tidak ada dosen/guru yang sesuai</p>
                        </div>
                        
                        <!-- Add New Button & Form -->
                        <button 
                            type="button"
                            @click="showAddTeacher = !showAddTeacher"
                            class="w-full mt-3 py-3 border-2 border-dashed rounded-lg text-gray-600 hover:border-[#b01116] hover:text-[#b01116] hover:bg-red-50 transition-all flex items-center justify-center gap-2 font-medium">
                            <i class="ri-add-circle-line text-xl"></i>
                            <span x-text="showAddTeacher ? 'Batal Tambah' : 'Tambah Dosen/Guru Baru'"></span>
                        </button>
                        
                        <div x-show="showAddTeacher" x-transition x-collapse class="mt-4 p-4 bg-gradient-to-br from-red-50 to-pink-50 rounded-lg border-2 border-red-200">
                            <h5 class="font-semibold text-gray-800 mb-3 flex items-center gap-2">
                                <i class="ri-add-line text-[#b01116] text-xl"></i>
                                Tambah Dosen/Guru Baru
                            </h5>
                            <div class="space-y-3">
                                <input 
                                    type="text" 
                                    x-model="newTeacher.name"
                                    placeholder="Nama Lengkap *"
                                    class="w-full px-4 py-2.5 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-[#b01116]">
                                <input 
                                    type="text" 
                                    x-model="newTeacher.nip"
                                    placeholder="NIP/NIDN (opsional)"
                                    class="w-full px-4 py-2.5 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-[#b01116]">
                                <div class="grid grid-cols-2 gap-3">
                                    <input 
                                        type="email" 
                                        x-model="newTeacher.email"
                                        placeholder="Email (opsional)"
                                        class="w-full px-4 py-2.5 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-[#b01116]">
                                    <input 
                                        type="text" 
                                        x-model="newTeacher.phone_number"
                                        placeholder="No. Telepon (opsional)"
                                        class="w-full px-4 py-2.5 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-[#b01116]">
                                </div>
                                <input 
                                    type="text" 
                                    x-model="newTeacher.institution"
                                    placeholder="Institusi/Sekolah (opsional)"
                                    class="w-full px-4 py-2.5 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-[#b01116]">
                                <button 
                                    type="button"
                                    @click="createTeacher()"
                                    class="w-full bg-[#b01116] hover:bg-[#8d0d11] text-white py-2.5 rounded-lg font-semibold transition-colors flex items-center justify-center gap-2 shadow-md">
                                    <i class="ri-save-line"></i>
                                    Simpan Dosen/Guru
                                </button>
                            </div>
                        </div>
                        
                        <div class="mt-4 p-3 bg-gradient-to-r from-red-50 to-pink-50 rounded-lg border border-red-200">
                            <div class="flex items-center gap-3 text-red-700">
                                <div class="w-10 h-10 rounded-full bg-[#8d0d11] text-white flex items-center justify-center font-bold shadow-sm">
                                    <span x-text="projectData.teachers.length"></span>
                                </div>
                                <span class="text-sm font-semibold">Pembimbing Dipilih</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Step 3: Team Members (for Team) or Media (for Individual) -->
                <div x-show="currentStep === 3" x-transition class="p-6 space-y-6">
                    <!-- Team Members Section (Only for Team Projects) -->
                    <div x-show="projectType === 'team'">
                        <h4 class="font-semibold text-gray-800 mb-3 flex items-center gap-2 text-lg">
                            <i class="ri-team-line text-[#b01116]"></i>
                            Anggota Tim <span class="text-[#b01116]">*</span>
                        </h4>
                        <p class="text-sm text-gray-600 mb-4">Pilih minimal 1 anggota tim (selain Anda sebagai leader)</p>
                        
                        <div class="relative mb-4">
                            <i class="ri-search-line absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                            <input type="text" x-model="searchStudent" placeholder="Cari mahasiswa..."
                                   class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#b01116]">
                        </div>
                        
                        <div class="space-y-3 max-h-96 overflow-y-auto">
                            <template x-for="student in filteredStudents" :key="student.id">
                                <div class="p-4 border-2 rounded-lg transition-all"
                                     :class="projectData.team_members.includes(student.id) ? 'border-[#b01116] bg-red-50' : 'border-gray-200 hover:border-gray-300'">
                                    <label class="flex items-start gap-3 cursor-pointer">
                                        <input type="checkbox" class="sr-only" 
                                               :checked="projectData.team_members.includes(student.id)"
                                               @change="toggleTeamMember(student.id)">
                                        <div class="w-5 h-5 rounded border-2 flex-shrink-0 mt-1 flex items-center justify-center"
                                             :class="projectData.team_members.includes(student.id) ? 'bg-[#b01116] border-[#b01116]' : 'border-gray-300'">
                                            <i class="ri-check-line text-white text-sm" x-show="projectData.team_members.includes(student.id)"></i>
                                        </div>
                                        <div class="flex items-center gap-3 flex-1">
                                            <div class="w-12 h-12 rounded-full bg-gradient-to-br from-[#b01116] to-pink-600 flex items-center justify-center text-white text-lg font-bold">
                                                <span x-text="student.user.username.charAt(0).toUpperCase()"></span>
                                            </div>
                                            <div class="flex-1">
                                                <div class="font-semibold text-gray-800" x-text="student.user.full_name || student.user.username"></div>
                                                <div class="text-sm text-gray-600">@<span x-text="student.user.username"></span></div>
                                            </div>
                                        </div>
                                    </label>
                                    
                                    <div x-show="projectData.team_members.includes(student.id)" x-transition class="mt-3 pl-8">
                                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Posisi dalam Tim *</label>
                                        <input type="text" x-model="projectData.team_positions[student.id]"
                                               placeholder="Contoh: Frontend Developer, UI Designer"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#b01116] text-sm">
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- Enhanced Media Management -->
                    <div>
                        <h4 class="font-semibold text-gray-800 mb-3 flex items-center gap-2 text-lg">
                            <i class="ri-image-line text-[#b01116]"></i>
                            Kelola Gambar Proyek <span class="text-[#b01116]">*</span>
                        </h4>
                        <p class="text-sm text-gray-600 mb-4">Minimal 1 gambar diperlukan untuk proyek</p>
                        
                        <!-- Existing Images Section -->
                        <div x-show="projectData.existing_images && projectData.existing_images.length > 0" class="mb-6">
                            <h5 class="font-semibold text-gray-700 mb-3 flex items-center gap-2">
                                <i class="ri-gallery-line text-[#b01116]"></i>
                                Gambar Saat Ini (<span x-text="projectData.existing_images.length"></span>)
                            </h5>
                            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4 mb-4">
                                <template x-for="(image, index) in projectData.existing_images" :key="'existing-' + index">
                                    <div class="relative group">
                                        <div class="aspect-square rounded-lg overflow-hidden border-2"
                                             :class="index === 0 ? 'border-[#b01116] ring-2 ring-red-100' : 'border-gray-300'">
                                            <img :src="image.url || image.file_path" 
                                                 :alt="image.alt_text || 'Project Image'" 
                                                 class="w-full h-full object-cover">
                                        </div>
                                        
                                        <!-- Action Buttons -->
                                        <div class="absolute top-2 right-2 flex gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                            <button type="button"
                                                    @click="setAsMainImage(index, 'existing')"
                                                    :disabled="index === 0"
                                                    :class="index === 0 ? 'opacity-50 cursor-not-allowed' : 'hover:bg-blue-600'"
                                                    class="bg-blue-500 text-white rounded-full p-1.5 text-xs transition-colors"
                                                    title="Set as main image">
                                                <i class="ri-star-line"></i>
                                            </button>
                                            <button type="button"
                                                    @click="markImageForDeletion(index)"
                                                    class="bg-red-600 text-white rounded-full p-1.5 transition-colors hover:bg-red-700"
                                                    title="Delete image">
                                                <i class="ri-close-line text-xs"></i>
                                            </button>
                                        </div>
                                        
                                        <!-- Main Image Badge -->
                                        <div x-show="index === 0" 
                                             class="absolute bottom-0 left-0 right-0 bg-[#b01116] text-white text-xs py-1 text-center font-medium">
                                            <i class="ri-star-fill mr-1"></i>Gambar Utama
                                        </div>
                                        
                                        <!-- Deletion Overlay -->
                                        <div x-show="projectData.images_to_delete && projectData.images_to_delete.includes(image.id || index)" 
                                             class="absolute inset-0 bg-red-600 bg-opacity-75 flex items-center justify-center rounded-lg">
                                            <div class="text-white text-center">
                                                <i class="ri-delete-bin-line text-2xl mb-1"></i>
                                                <p class="text-xs font-medium">Akan Dihapus</p>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                        
                        <!-- Add New Images Section -->
                        <div x-data="mediaPreview('edit_media')">
                            <h5 class="font-semibold text-gray-700 mb-3 flex items-center gap-2">
                                <i class="ri-add-circle-line text-[#b01116]"></i>
                                Tambah Gambar Baru
                            </h5>
                            <div class="border-2 border-dashed border-gray-300 rounded-xl p-6 text-center hover:border-[#b01116] hover:bg-red-50 transition-all">
                                <i class="ri-upload-cloud-2-line text-4xl text-gray-400 mb-3"></i>
                                <div class="text-sm text-gray-600 mb-3 font-medium">
                                    Drop files here or click to upload
                                </div>
                                <input type="file" 
                                       name="new_media[]" 
                                       multiple 
                                       accept="image/*" 
                                       class="hidden" 
                                       id="edit_media"
                                       @change="handleFiles($event.target.files)">
                                <label for="edit_media" class="inline-flex items-center gap-2 cursor-pointer bg-[#b01116] text-white px-4 py-2.5 rounded-lg hover:bg-[#8d0d11] transition-colors font-medium shadow-md hover:shadow-lg">
                                    <i class="ri-folder-open-line"></i>
                                    Pilih Gambar
                                </label>
                                <div class="text-xs text-gray-500 mt-3">
                                    Max 10 files • Each up to 10MB • JPG, PNG, GIF
                                </div>
                            </div>
                            
                            <!-- New Images Preview -->
                            <div x-show="previews.length > 0" class="mt-4">
                                <p class="text-sm font-medium text-gray-700 mb-3">
                                    Gambar Baru (<span x-text="previews.length"></span>)
                                    <span class="text-xs text-gray-500 ml-2">• Akan ditambahkan setelah gambar yang ada</span>
                                </p>
                                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                                    <template x-for="(preview, index) in previews" :key="'new-' + index">
                                        <div class="relative group">
                                            <div class="aspect-square rounded-lg overflow-hidden border-2 border-green-300">
                                                <img :src="preview.url" 
                                                     :alt="preview.name" 
                                                     class="w-full h-full object-cover">
                                            </div>
                                            <button type="button"
                                                    @click="removeFile(index)"
                                                    class="absolute top-2 right-2 bg-red-600 text-white rounded-full p-1.5 opacity-0 group-hover:opacity-100 transition-opacity hover:bg-red-700">
                                                <i class="ri-close-line text-xs"></i>
                                            </button>
                                            <div class="absolute bottom-0 left-0 right-0 bg-green-600 text-white text-xs py-1 text-center font-medium">
                                                <i class="ri-add-line mr-1"></i>Baru
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Image Summary -->
                        <div class="mt-4 p-4 rounded-lg border-2"
                             :class="getTotalImagesCount() === 0 ? 'bg-red-50 border-red-200' : 'bg-green-50 border-green-200'">
                            <div class="flex items-center gap-3"
                                 :class="getTotalImagesCount() === 0 ? 'text-red-700' : 'text-green-700'">
                                <i :class="getTotalImagesCount() === 0 ? 'ri-error-warning-line text-xl' : 'ri-checkbox-circle-line text-xl'"></i>
                                <div class="flex-1">
                                    <div class="font-semibold">
                                        <span x-show="getTotalImagesCount() === 0">Minimal 1 gambar diperlukan</span>
                                        <span x-show="getTotalImagesCount() > 0">
                                            Total: <span x-text="getTotalImagesCount()"></span> gambar
                                        </span>
                                    </div>
                                    <div class="text-sm mt-1" x-show="getTotalImagesCount() > 0">
                                        <span x-text="getExistingImagesCount() + ' gambar saat ini'"></span>
                                        <span x-show="previews.length > 0"> • <span x-text="previews.length + ' gambar baru'"></span></span>
                                        <span x-show="getDeletedImagesCount() > 0"> • <span x-text="getDeletedImagesCount() + ' akan dihapus'"></span></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Enhanced Review Section with AS-IS vs TO-BE -->
                    <div class="bg-gradient-to-br from-gray-50 to-gray-100 rounded-xl p-6 border-2 border-gray-200">
                        <h4 class="font-semibold text-gray-800 mb-4 flex items-center gap-2 text-lg">
                            <i class="ri-file-list-3-line text-[#b01116]"></i>
                            Review Perubahan
                        </h4>
                        <div class="space-y-4 text-sm">
                            
                            <!-- Title Comparison -->
                            <div class="p-4 rounded-lg border"
                                 :class="hasChanged('title') ? 'bg-yellow-50 border-yellow-300' : 'bg-white border-gray-200'">
                                <div class="flex items-center gap-2 mb-2">
                                    <i class="ri-file-text-line" :class="hasChanged('title') ? 'text-yellow-600' : 'text-gray-400'"></i>
                                    <span class="font-semibold text-gray-700">Judul Proyek</span>
                                    <span x-show="hasChanged('title')" class="text-xs bg-yellow-200 text-yellow-800 px-2 py-1 rounded-full">Berubah</span>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                    <div>
                                        <p class="text-xs text-gray-500 mb-1">Sebelum:</p>
                                        <p class="text-gray-600 font-medium" x-text="getOriginalValue('title') || 'Belum diisi'"></p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500 mb-1">Sesudah:</p>
                                        <p class="text-gray-800 font-medium" x-text="projectData.title || 'Belum diisi'"></p>
                                    </div>
                                </div>
                            </div>

                            <!-- Description Comparison -->
                            <div class="p-4 rounded-lg border"
                                 :class="hasChanged('description') ? 'bg-yellow-50 border-yellow-300' : 'bg-white border-gray-200'">
                                <div class="flex items-center gap-2 mb-2">
                                    <i class="ri-file-text-line" :class="hasChanged('description') ? 'text-yellow-600' : 'text-gray-400'"></i>
                                    <span class="font-semibold text-gray-700">Deskripsi Proyek</span>
                                    <span x-show="hasChanged('description')" class="text-xs bg-yellow-200 text-yellow-800 px-2 py-1 rounded-full">Berubah</span>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                    <div>
                                        <p class="text-xs text-gray-500 mb-1">Sebelum:</p>
                                        <p class="text-gray-600 text-sm" x-text="getOriginalValue('description') ? (getOriginalValue('description').substring(0, 100) + (getOriginalValue('description').length > 100 ? '...' : '')) : 'Belum diisi'"></p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500 mb-1">Sesudah:</p>
                                        <p class="text-gray-800 text-sm" x-text="projectData.description ? (projectData.description.substring(0, 100) + (projectData.description.length > 100 ? '...' : '')) : 'Belum diisi'"></p>
                                    </div>
                                </div>
                            </div>

                            <!-- Price Comparison -->
                            <div class="p-4 rounded-lg border"
                                 :class="hasChanged('price') ? 'bg-yellow-50 border-yellow-300' : 'bg-white border-gray-200'">
                                <div class="flex items-center gap-2 mb-2">
                                    <i class="ri-money-dollar-circle-line" :class="hasChanged('price') ? 'text-yellow-600' : 'text-gray-400'"></i>
                                    <span class="font-semibold text-gray-700">Estimasi Harga</span>
                                    <span x-show="hasChanged('price')" class="text-xs bg-yellow-200 text-yellow-800 px-2 py-1 rounded-full">Berubah</span>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                    <div>
                                        <p class="text-xs text-gray-500 mb-1">Sebelum:</p>
                                        <p class="text-gray-600 font-medium" x-text="getOriginalValue('price') ? 'Rp ' + new Intl.NumberFormat('id-ID').format(getOriginalValue('price')) : 'Tidak diisi'"></p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500 mb-1">Sesudah:</p>
                                        <p class="text-gray-800 font-medium" x-text="projectData.price ? 'Rp ' + new Intl.NumberFormat('id-ID').format(projectData.price) : 'Tidak diisi'"></p>
                                    </div>
                                </div>
                            </div>

                            <!-- Status Comparison -->
                            <div class="p-4 rounded-lg border"
                                 :class="hasChanged('status') ? 'bg-yellow-50 border-yellow-300' : 'bg-white border-gray-200'">
                                <div class="flex items-center gap-2 mb-2">
                                    <i class="ri-eye-line" :class="hasChanged('status') ? 'text-yellow-600' : 'text-gray-400'"></i>
                                    <span class="font-semibold text-gray-700">Status Publikasi</span>
                                    <span x-show="hasChanged('status')" class="text-xs bg-yellow-200 text-yellow-800 px-2 py-1 rounded-full">Berubah</span>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                    <div>
                                        <p class="text-xs text-gray-500 mb-1">Sebelum:</p>
                                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded text-xs font-medium"
                                              :class="getOriginalValue('status') === 'published' ? 'bg-red-100 text-red-700' : 'bg-gray-100 text-gray-700'"
                                              x-text="getOriginalValue('status') === 'draft' ? 'Draft' : getOriginalValue('status') === 'published' ? 'Published' : 'Archived'"></span>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500 mb-1">Sesudah:</p>
                                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded text-xs font-medium"
                                              :class="projectData.status === 'published' ? 'bg-red-100 text-red-700' : 'bg-gray-100 text-gray-700'"
                                              x-text="projectData.status === 'draft' ? 'Draft' : projectData.status === 'published' ? 'Published' : 'Archived'"></span>
                                    </div>
                                </div>
                            </div>

                            <!-- Categories Comparison -->
                            <div class="p-4 rounded-lg border"
                                 :class="hasChanged('categories') ? 'bg-yellow-50 border-yellow-300' : 'bg-white border-gray-200'">
                                <div class="flex items-center gap-2 mb-2">
                                    <i class="ri-price-tag-3-line" :class="hasChanged('categories') ? 'text-yellow-600' : 'text-gray-400'"></i>
                                    <span class="font-semibold text-gray-700">Kategori</span>
                                    <span x-show="hasChanged('categories')" class="text-xs bg-yellow-200 text-yellow-800 px-2 py-1 rounded-full">Berubah</span>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                    <div>
                                        <p class="text-xs text-gray-500 mb-1">Sebelum:</p>
                                        <p class="text-gray-600 font-medium" x-text="getOriginalValue('categories') + ' kategori dipilih'"></p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500 mb-1">Sesudah:</p>
                                        <p class="text-gray-800 font-medium" x-text="projectData.categories.length + ' kategori dipilih'"></p>
                                    </div>
                                </div>
                            </div>

                            <!-- Subjects Comparison -->
                            <div class="p-4 rounded-lg border"
                                 :class="hasChanged('subjects') ? 'bg-yellow-50 border-yellow-300' : 'bg-white border-gray-200'">
                                <div class="flex items-center gap-2 mb-2">
                                    <i class="ri-book-open-line" :class="hasChanged('subjects') ? 'text-yellow-600' : 'text-gray-400'"></i>
                                    <span class="font-semibold text-gray-700">Mata Kuliah</span>
                                    <span x-show="hasChanged('subjects')" class="text-xs bg-yellow-200 text-yellow-800 px-2 py-1 rounded-full">Berubah</span>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                    <div>
                                        <p class="text-xs text-gray-500 mb-1">Sebelum:</p>
                                        <p class="text-gray-600 font-medium" x-text="getOriginalValue('subjects') + ' mata kuliah terpilih'"></p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500 mb-1">Sesudah:</p>
                                        <p class="text-gray-800 font-medium" x-text="projectData.subjects.length + ' mata kuliah terpilih'"></p>
                                    </div>
                                </div>
                            </div>

                            <!-- Teachers Comparison -->
                            <div class="p-4 rounded-lg border"
                                 :class="hasChanged('teachers') ? 'bg-yellow-50 border-yellow-300' : 'bg-white border-gray-200'">
                                <div class="flex items-center gap-2 mb-2">
                                    <i class="ri-user-star-line" :class="hasChanged('teachers') ? 'text-yellow-600' : 'text-gray-400'"></i>
                                    <span class="font-semibold text-gray-700">Pembimbing</span>
                                    <span x-show="hasChanged('teachers')" class="text-xs bg-yellow-200 text-yellow-800 px-2 py-1 rounded-full">Berubah</span>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                    <div>
                                        <p class="text-xs text-gray-500 mb-1">Sebelum:</p>
                                        <p class="text-gray-600 font-medium" x-text="getOriginalValue('teachers') + ' pembimbing terpilih'"></p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500 mb-1">Sesudah:</p>
                                        <p class="text-gray-800 font-medium" x-text="projectData.teachers.length + ' pembimbing terpilih'"></p>
                                    </div>
                                </div>
                            </div>

                            <!-- Team Members Comparison (for team projects) -->
                            <div x-show="projectType === 'team'" 
                                 class="p-4 rounded-lg border"
                                 :class="hasChanged('team_members') ? 'bg-yellow-50 border-yellow-300' : 'bg-white border-gray-200'">
                                <div class="flex items-center gap-2 mb-2">
                                    <i class="ri-team-line" :class="hasChanged('team_members') ? 'text-yellow-600' : 'text-gray-400'"></i>
                                    <span class="font-semibold text-gray-700">Anggota Tim</span>
                                    <span x-show="hasChanged('team_members')" class="text-xs bg-yellow-200 text-yellow-800 px-2 py-1 rounded-full">Berubah</span>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                    <div>
                                        <p class="text-xs text-gray-500 mb-1">Sebelum:</p>
                                        <p class="text-gray-600 font-medium" x-text="getOriginalValue('team_members') + ' anggota (+ 1 leader)'"></p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500 mb-1">Sesudah:</p>
                                        <p class="text-gray-800 font-medium" x-text="projectData.team_members.length + ' anggota (+ 1 leader)'"></p>
                                    </div>
                                </div>
                            </div>

                            <!-- Images Comparison -->
                            <div class="p-4 rounded-lg border"
                                 :class="hasImagesChanged() ? 'bg-yellow-50 border-yellow-300' : 'bg-white border-gray-200'">
                                <div class="flex items-center gap-2 mb-3">
                                    <i class="ri-image-line" :class="hasImagesChanged() ? 'text-yellow-600' : 'text-gray-400'"></i>
                                    <span class="font-semibold text-gray-700">Gambar Proyek</span>
                                    <span x-show="hasImagesChanged()" class="text-xs bg-yellow-200 text-yellow-800 px-2 py-1 rounded-full">Berubah</span>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <p class="text-xs text-gray-500 mb-2">Sebelum:</p>
                                        <div class="text-gray-600">
                                            <p class="font-medium" x-text="(projectData.existing_images ? projectData.existing_images.length : 0) + ' gambar'"></p>
                                            <div x-show="projectData.existing_images && projectData.existing_images.length > 0" class="mt-2 grid grid-cols-3 gap-1">
                                                <template x-for="(image, index) in (projectData.existing_images || []).slice(0, 3)" :key="'review-existing-' + index">
                                                    <div class="aspect-square rounded border overflow-hidden">
                                                        <img :src="image.url || image.file_path" class="w-full h-full object-cover" :alt="'Image ' + (index + 1)">
                                                    </div>
                                                </template>
                                            </div>
                                        </div>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500 mb-2">Sesudah:</p>
                                        <div class="text-gray-800">
                                            <p class="font-medium" x-text="getTotalImagesCount() + ' gambar'"></p>
                                            <div class="text-xs mt-1 space-y-1">
                                                <p x-show="getExistingImagesCount() > 0" class="flex items-center gap-1">
                                                    <span class="w-2 h-2 bg-blue-500 rounded-full"></span>
                                                    <span x-text="getExistingImagesCount() + ' gambar tetap'"></span>
                                                </p>
                                                <p x-show="getDeletedImagesCount() > 0" class="flex items-center gap-1">
                                                    <span class="w-2 h-2 bg-red-500 rounded-full"></span>
                                                    <span x-text="getDeletedImagesCount() + ' gambar dihapus'"></span>
                                                </p>
                                                <p x-show="previews.length > 0" class="flex items-center gap-1">
                                                    <span class="w-2 h-2 bg-green-500 rounded-full"></span>
                                                    <span x-text="previews.length + ' gambar baru'"></span>
                                                </p>
                                            </div>
                                            <div x-show="previews.length > 0" class="mt-2 grid grid-cols-3 gap-1">
                                                <template x-for="(preview, index) in previews.slice(0, 3)" :key="'review-new-' + index">
                                                    <div class="aspect-square rounded border-2 border-green-300 overflow-hidden">
                                                        <img :src="preview.url" class="w-full h-full object-cover" :alt="preview.name">
                                                    </div>
                                                </template>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal Footer with Navigation -->
                <div class="sticky bottom-0 bg-white border-t border-gray-200 px-6 py-4">
                    <div class="flex justify-between items-center gap-4">
                        <button type="button" 
                                @click="prevStep()" 
                                x-show="currentStep > 1"
                                class="px-6 py-2.5 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium transition-colors flex items-center gap-2">
                            <i class="ri-arrow-left-line"></i>Kembali
                        </button>
                        
                        <button type="button" 
                                @click="showEditProjectModal = false; resetProjectModal()" 
                                class="px-6 py-2.5 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium transition-colors">
                            Batal
                        </button>
                        
                        <button type="button" 
                                @click="nextStep()" 
                                x-show="currentStep < totalSteps"
                                :disabled="!canProceedToNext()"
                                :class="!canProceedToNext() ? 'opacity-50 cursor-not-allowed' : 'hover:bg-[#8d0d11]'"
                                class="px-6 py-2.5 bg-[#b01116] text-white rounded-lg font-medium transition-colors flex items-center gap-2">
                            Selanjutnya<i class="ri-arrow-right-line"></i>
                        </button>
                        
                        <button type="button" 
                                @click="updateProject()"
                                x-show="currentStep === totalSteps"
                                :disabled="!canCreateProject()"
                                :class="!canCreateProject() ? 'opacity-50 cursor-not-allowed' : 'hover:bg-[#8d0d11]'"
                                class="px-6 py-2.5 bg-[#b01116] text-white rounded-lg font-medium transition-colors flex items-center gap-2">
                            <i class="ri-save-line"></i>Simpan Perubahan
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

    </div>
</div>

<script>
function mediaPreview(inputId) {
    return {
        previews: [],
        files: [],
        inputId: inputId,
        
        handleFiles(fileList) {
            this.files = Array.from(fileList);
            this.previews = [];
            
            this.files.forEach((file, index) => {
                if (file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        this.previews.push({
                            url: e.target.result,
                            name: file.name,
                            type: file.type,
                            size: file.size
                        });
                    };
                    reader.readAsDataURL(file);
                }
            });
        },
        
        removeFile(index) {
            this.previews.splice(index, 1);
            this.files.splice(index, 1);
            
            // Update the SPECIFIC file input using the inputId
            const fileInput = document.getElementById(this.inputId);
            if (fileInput) {
                const dt = new DataTransfer();
                this.files.forEach(file => {
                    dt.items.add(file);
                });
                fileInput.files = dt.files;
            }
        }
    }
}
</script>
@endsection
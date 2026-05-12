<script setup>
import { Head, useForm } from "@inertiajs/vue3";
import { index, update } from '@/actions/App/Http/Controllers/NoteController';
import { toast } from 'vue-sonner';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import InputError from '@/components/InputError.vue';
import SharedWithHelp from './components/SharedWithHelp.vue';

const props = defineProps({
  note: {
    type: Object,
    required: true,
  },
  users: {
    type: Object,
    required: true,
  },
});

defineOptions({
  layout: {
    breadcrumbs: [
      {
        title: 'Notes',
        href: index(),
      },
      {
        title: 'Edit Note',
      },
    ],
  },
});

const form = useForm({
  id: props.note.data.id,
  title: props.note.data.title,
  details: props.note.data.details,
  sharedWith: new Set(props.note.data.sharedWith.map((user) => user.id)),
});

function setSharedWith(userId, shared) {
  if (shared) {
    form.sharedWith.add(userId);
  } else {
    form.sharedWith.delete(userId);
  }
}

function showErrorToast() {
  toast.error('Failed to save changes');
}

function submit() {
  if (form.processing) {
    return;
  }
  const { method, url } = update(form.id);
  form
    .transform((data) => ({
      ...data,
      sharedWith: data.sharedWith.values().toArray(),
    }))
    .submit(method, url, {
      onHttpException: showErrorToast,
      onNetworkError: showErrorToast,
  });
}
</script>

<template>
  <Head title="Edit Note" />
  
  <h1 class="text-2xl font-semibold mb-8">Edit Note</h1>
  
  <form
    @submit.prevent="submit()"
    class="max-w-lg space-y-4"
  >
    <div class="space-y-3">
      <Label for="title">Title</Label>
      <Input id="title" v-model="form.title" />
      <InputError :message="form.errors.title" />
    </div>

    <div class="space-y-3">
      <Label for="details">Details</Label>
      <Textarea
        id="details"
        v-model="form.details"
        placeholder="Type note details here."
        class="h-64"
      />
      <InputError :message="form.errors.details" />
    </div>

    <div class="space-y-3">
      <div class="flex items-center gap-2">
        <Label>Shared With</Label>
        <SharedWithHelp />
      </div>
      <div
        v-for="user in users.data"
        :key="user.id"
        class="flex items-center gap-3"
      >
        <Checkbox
          :id="user.email"
          :model-value="form.sharedWith.has(user.id)"
          @update:model-value="(checked) => setSharedWith(user.id, checked)"
        />
        <Label :for="user.email">{{ user.email }}</Label>
      </div>
    </div>

    <div class="flex items-center gap-4">
      <Button
        type="submit"
        :disabled="!form.isDirty || form.processing"
        class="my-4"
      >
        {{ form.processing ? 'Saving...' : 'Save' }}
      </Button>
      <span v-if="!form.isDirty" class="text-sm italic">
        No unsaved changes
      </span>
    </div>
  </form>
</template>

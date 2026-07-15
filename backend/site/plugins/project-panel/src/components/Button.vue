<template>
  <input :class="['button', `is-${content.style}`]" :data-alignment="content.alignment" type="text"
    :placeholder="placeholder" :value="content.text" @input="update({ text: $event.target.value })" />
</template>

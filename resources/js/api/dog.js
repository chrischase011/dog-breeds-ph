'use strict';

import { dogApiUrl } from '../config';

export async function fetchBreeds() {
  try {
    const response = await fetch(`${dogApiUrl}breeds/list/all`);
    if (!response.ok) {
      throw new Error('Network response was not ok');
    }

    const data = await response.json();
    return data.message;
  } catch (error) {
    console.error('Error fetching breeds:', error);
    throw error;
  }
}

export async function fetchBreedImage(breed) {
  try {
    const response = await fetch(`${dogApiUrl}breed/${breed}/images/random`);
    if (!response.ok) {
      throw new Error('Network response was not ok');
    }

    const data = await response.json();
    return data.message;
  } catch (error) {
    console.error(`Error fetching image for breed ${breed}:`, error);
    throw error;
  }
}

export async function fetchDogBreeds() {
  try {
    const breedList = await fetchBreeds();

    const breeds = Object.keys(breedList);
    const breedWithImages = await Promise.all(
      breeds.map(async (breed) => {
        const image = await fetchBreedImage(breed);
        return {
          name: breed.charAt(0).toUpperCase() + breed.slice(1),
          image
        };
      })
    );

    return breedWithImages;
  } catch (error) {
    console.error('Error fetching dog breeds with images:', error);
    return [];
  }
}
